export default class Product {
    id: number
    name: string
    price: number
    color: string
    icon: string | null = null
    imageUrl: string | null = null

    constructor(id: number, name: string, price: number, color: string, icon: string | null, imageUrl: string | null) {
        this.id = id
        this.name = name
        this.price = price
        this.color = color
        this.icon = icon
        this.imageUrl = imageUrl
    }
}
