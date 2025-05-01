export type TransactionState = Type & (None | Success | Pending | Sending);

interface Type {
    kind: String
}

export class None implements Type {
    kind: String = 'None'
}

export class Success implements Type {
    kind: String = 'Success'
}

export class Pending implements Type {
    kind: String = 'Pending'
    count: number

    constructor(count: number) {
        this.count = count;
    }
}

export class Sending extends Pending implements Type {
    kind: String = 'Sending'
}
