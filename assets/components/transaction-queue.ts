import type {Transaction} from "../model/transaction.ts";

export const storeName = 'transaction_queue';

type TableRow = { name: string, unique: boolean }

export class TransactionQueue {
    db: IDBDatabase | null = null;
    isInitialised: boolean = false;
    items: Array<Transaction> = [];
    tables: Record<string, Array<TableRow>> = {
        transactions: [
            {name: 'uuid', unique: true},
            {name: 'paymentType', unique: false},
            {name: 'cart', unique: false},
        ],
    };

    public async length(): Promise<number> {
        if (!this.isInitialised) {
            this.items = await this.getAll()
        }

        return this.items.length
    }

    public async push(transaction: Transaction): Promise<void> {
        const db = await this.getDb()
        return new Promise((resolve, reject) => {
            const request = db
                .transaction(storeName, 'readwrite')
                .objectStore(storeName)
                .add({
                    uuid: transaction.id,
                    paymentType: transaction.paymentType,
                    cart: JSON.stringify(transaction.cart)
                });

            request.onerror = err => {
                console.error(`[IndexedDB] error: adding entry`, err)
                reject('Error')
            }
            request.onsuccess = () => {
                if (this.isInitialised) {
                    this.items.push(transaction)
                }
                resolve()
            }
        })
    }

    public async delete(uuid: string): Promise<void> {
        const db = await this.getDb()
        return new Promise((resolve, reject) => {
            const request = db
                .transaction(storeName, 'readwrite')
                .objectStore(storeName)
                .delete(uuid);

            request.onerror = err => {
                console.error(`[IndexedDB] error: deleting entry`, err)
                reject('Error')
            }
            request.onsuccess = () => {
                if (this.isInitialised) {
                    this.items = this.items.filter(item => item.id !== uuid)
                }
                resolve()
            }
        })
    }

    public async getAll(): Promise<Array<Transaction>> {
        if (!this.isInitialised) {
            this.items = await this.loadAll()
            this.isInitialised = true
        }

        return this.items
    }

    private async getDb(): Promise<IDBDatabase> {
        if (this.db !== null) {
            return this.db
        }

        return new Promise((resolve, reject) => {
            const request = window.indexedDB.open("transaction_queue", 3)
            request.onsuccess = () => {
                this.db = request.result
                resolve(this.db)
            }

            request.onerror = (err) => {
                console.error(`[IndexedDB] error: ${request.error}`, err)
                reject(err)
            }

            request.onupgradeneeded = () => {
                const db = request.result;
                for (const name in this.tables) {
                    const row = this.tables[name];
                    const transactionsStore = db.createObjectStore(storeName, {keyPath: row[0].name});
                    row.forEach((key) => transactionsStore.createIndex(key.name, key.name, {unique: key.unique}));
                }
            }
        })
    }

    private async loadAll(): Promise<Array<Transaction>> {
        const db = await this.getDb()
        return new Promise((resolve, reject) => {
            const request = db
                .transaction(storeName, 'readonly')
                .objectStore(storeName)
                .getAll();

            request.onerror = (err) => {
                console.error(`[IndexedDB] error: reading entries,`, err)
                reject('Error')
            }

            request.onsuccess = () => {
                const transactions: Array<Transaction> = request.result.map((data) => {
                    return {
                        id: data.uuid,
                        paymentType: data.paymentType,
                        cart: JSON.parse(data.cart),
                    } as Transaction;
                })

                resolve(transactions)
            }
        })
    }
}

export const transactionQueue = new TransactionQueue();
