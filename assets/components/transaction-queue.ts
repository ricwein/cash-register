import type {Transaction} from "../model/transaction.ts";

export const storeName = 'transaction_queue';

type TableRow = { name: string, unique: boolean }

export class TransactionQueue {
    db: IDBDatabase | null = null;
    tables: Record<string, Array<TableRow>> = {
        transactions: [
            {name: 'uuid', unique: true},
            {name: 'paymentType', unique: false},
            {name: 'cart', unique: false},
        ],
    };

    constructor() {
        const request = window.indexedDB.open("transaction_queue", 3);
        request.onsuccess = () => (this.db = request.result);
        request.onerror = (err) => console.error(`[IndexedDB] error: ${request.error}`, err);
        request.onupgradeneeded = () => {
            const db = request.result;
            for (const name in this.tables) {
                const row = this.tables[name];
                const transactionsStore = db.createObjectStore(storeName, {keyPath: row[0].name});
                row.forEach((key) => transactionsStore.createIndex(key.name, key.name, {unique: key.unique}));
            }
        };
    }

    public async count(): Promise<number> {
        return new Promise((resolve, reject) => {
            if (this.db === null) {
                reject('Not initialized.')
                return
            }

            const request = this.db
                .transaction(storeName, 'readonly')
                .objectStore(storeName)
                .count();

            request.onerror = err => {
                console.error('[IndexedDB] error: opening db', err);
                reject('Error');
            };
            request.onsuccess = () => {
                resolve(request.result)
            }
        })
    }

    public async add(transaction: Transaction): Promise<void> {
        return new Promise((resolve, reject) => {
            if (this.db === null) {
                reject('Not initialized.')
                return
            }

            const request = this.db
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
                resolve()
            }
        })
    }

    public delete(uuid: string): Promise<void> {
        return new Promise((resolve, reject) => {
            if (this.db === null) {
                reject('Not initialized.')
                return
            }
            const request = this.db.transaction(storeName, 'readwrite')
                .objectStore(storeName)
                .delete(uuid);

            request.onerror = err => {
                console.error(`[IndexedDB] error: deleting entry`, err)
                reject('Error')
            }
            request.onsuccess = () => {
                resolve()
            }
        })
    }

    public async getAll(): Promise<Array<Transaction>> {
        return new Promise((resolve, reject) => {
            if (this.db === null) {
                reject('Not initialized.')
                return
            }

            const request = this.db
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
