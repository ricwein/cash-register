import type {Transaction} from "../model/transaction.ts";
import {transactionQueue, type TransactionQueue} from "./transaction-queue.ts";
import {CheckoutTransition} from "./checkout-state-machine.ts";

type SendResponse = { state: CheckoutTransition, message: string }

export default class Transactor {
    queue: TransactionQueue
    endpointUrl: string

    constructor(endpointUrl: string) {
        this.endpointUrl = endpointUrl
        this.queue = transactionQueue
    }

    public async send(transaction: Transaction): Promise<SendResponse> {
        try {
            const result = await this.sendTransaction(transaction)

            if (result.state === CheckoutTransition.Error) {
                console.error(`[Transactor] Error: ${result.message}`)
            }

            return result;
        } catch (error) {
            const message = `${error}`
            const isRetryable = message.includes('NetworkError') || message.includes('timed out')
            console.error(`[Transactor] Error: `, error, {retryable: isRetryable})

            if (isRetryable) {
                await this.queue.push(transaction)
                return {state: CheckoutTransition.RetryableError, message: 'Server not reachable'}
            }

            return {state: CheckoutTransition.Error, message}
        }
    }

    public async sendAll(callback: (current: number, of: number) => void): Promise<void> {
        const items = await this.queue.getAll()
        const max = items.length
        let current = 1
        for (const transaction of items) {
            await new Promise(r => setTimeout(r, 2000))
            this.sendTransaction(transaction).then(() => {
                this.queue.delete(transaction.id)
                callback(current, max)
                current++
            })

        }
    }

    private async sendTransaction(transaction: Transaction): Promise<SendResponse> {
        const response = await fetch(this.endpointUrl, {
            method: 'PUT',
            body: JSON.stringify({
                uuid: transaction.id,
                paymentType: transaction.paymentType,
                cart: transaction.cart,
            }),
            headers: {
                'Content-Type': 'application/json',
            },
            signal: AbortSignal.timeout(3000)
        });
        const json = await response.json();

        const message = json.hasOwnProperty('message') ? json.message : '[unknown]'
        const stateString = json.hasOwnProperty('state') ? json.state : 'error'

        const state = stateString === 'success'
            ? CheckoutTransition.Success
            : CheckoutTransition.Error

        return {state, message}
    }
}
