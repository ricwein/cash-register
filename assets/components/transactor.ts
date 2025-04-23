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
                await this.queue.add(transaction)
            }

            return result;
        } catch (error) {
            return {state: CheckoutTransition.Error, message: `${error}`}
        }
    }

    public async sendAll(): Promise<void> {
        this.queue.getAll().then(transactions => {
            transactions.forEach(transaction => {
                this.sendTransaction(transaction)
            })
        })
    }

    private async sendTransaction(transaction: Transaction): Promise<SendResponse> {
        return new Promise((resolve, reject) => {
            fetch(this.endpointUrl, {
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
            })
                .then(response => response.json())
                .then(response => {
                    const message = response.hasOwnProperty('message') ? response.message : '[unknown]'
                    const stateString = response.hasOwnProperty('state') ? response.state : 'error'

                    const state = stateString === 'success'
                        ? CheckoutTransition.Success
                        : CheckoutTransition.Error

                    resolve({state, message})
                })
                .catch(error => {
                    reject(error)
                })
        })
    }
}
