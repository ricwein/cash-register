import {v4 as UUIDv4} from 'uuid';

export class Transaction {
    id: string
    paymentType: string
    cart: Record<number, number>

    constructor(paymentType: string, cart: Record<number, number>) {
        this.id = UUIDv4()
        this.paymentType = paymentType
        this.cart = cart
    }
}
