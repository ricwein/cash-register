import Product from "./product";

export default class Category {
    id: number
    name: string
    color: string
    products: Product[] = []

    constructor(id: number, name: string, color: string, products: Product[]) {
        this.id = id
        this.name = name
        this.color = color
        this.products = products
    }
}
