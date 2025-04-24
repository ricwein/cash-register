import type {Transaction} from "../model/transaction.ts";
import {transactionQueue, type TransactionQueue} from "./transaction-queue.ts";
import {CheckoutTransition} from "./checkout-state-machine.ts";

type SendResponse = { state: CheckoutTransition, message: string }

export default class Transactor {
    queue: TransactionQueue
    endpointUrl: string
    enqueuedMessage: string

    constructor(endpointUrl: string, enqueuedMessage: string = 'Transaction queued') {
        this.endpointUrl = endpointUrl
        this.queue = transactionQueue
        this.enqueuedMessage = enqueuedMessage
    }

    public async send(transaction: Transaction): Promise<SendResponse> {
        try {
            const result = await this.sendTransaction(transaction)

            if (result.state === CheckoutTransition.RetryableError) {
                await this.queue.push(transaction)
            } else if (result.state === CheckoutTransition.Error) {
                console.error(`TransactionError: ${result.message}`)
            }

            return result;
        } catch (error) {
            const message = `${error}`
            const isRetryable = message.includes('NetworkError') || message.includes('timed out')
            console.error(error, {retryable: isRetryable})

            if (isRetryable) {
                await this.queue.push(transaction)
                return {state: CheckoutTransition.RetryableError, message: this.enqueuedMessage}
            }

            return {state: CheckoutTransition.Error, message}
        }
    }

    public async sendAll(
        callback: (current: number, of: number) => void
    ): Promise<{ success: number, max: number }> {
        const items = await this.queue.getAll()
        const max = items.length
        let current = 0
        let success: number = 0

        await Promise.all(items.map(async transaction => {
            try {
                const result = await this.sendTransaction(transaction)
                callback(++current, max)
                if (result.state === CheckoutTransition.Success) {
                    await this.queue.delete(transaction.id)
                    success++
                }
            } catch (error) {
                callback(++current, max)
            }
        }))

        return {success, max}
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
        const state = stateString as CheckoutTransition

        return {state, message}
    }
}
