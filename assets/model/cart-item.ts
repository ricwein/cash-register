import type Product from './product.ts'

export default interface CartItem {
    product: Product
    quantity: number
}
