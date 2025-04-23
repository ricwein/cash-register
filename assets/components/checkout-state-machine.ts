import {type Ref, ref} from 'vue'

export const enum CheckoutTransition {
    Start = 'start',
    Cancel = 'cancel',
    Cash = 'cash',
    Card = 'card',
    Skip = 'skip',
    Back = 'back',
    Error = 'error',
    RetryableError = 'retryable',
    Success = 'success',
    Execute = 'execute',
}

export const enum CheckoutState {
    Off = 'off',
    Check = 'check',
    Calculator = 'calculator',
    Confirm = 'confirm',
    Sending = 'sending',
    Failed = 'failed',
}

export type StateChange = { fromState: CheckoutState, transition: CheckoutTransition, toState: CheckoutState, storage: any | null }
type TransitionAction = (() => CheckoutState)
type StateChangeCallback = (change: StateChange) => void
type StateChangeTrigger = CheckoutTransition | CheckoutState

export class CheckoutStateMachine {
    private state = ref<CheckoutState>(CheckoutState.Off)
    private anywayCallbacks: StateChangeCallback[] = [];
    private callbacks: Record<StateChangeTrigger, StateChangeCallback[]> = {
        [CheckoutState.Off]: [],
        [CheckoutState.Check]: [],
        [CheckoutState.Calculator]: [],
        [CheckoutState.Confirm]: [],
        [CheckoutState.Sending]: [],
        [CheckoutState.Failed]: [],
        [CheckoutTransition.Start]: [],
        [CheckoutTransition.Cancel]: [],
        [CheckoutTransition.Cash]: [],
        [CheckoutTransition.Card]: [],
        [CheckoutTransition.Skip]: [],
        [CheckoutTransition.Back]: [],
        [CheckoutTransition.Error]: [],
        [CheckoutTransition.RetryableError]: [],
        [CheckoutTransition.Success]: [],
        [CheckoutTransition.Execute]: [],
    };
    private storage: any | null = null

    private transitions: Record<CheckoutState, Partial<Record<CheckoutTransition, TransitionAction>>> = {
        [CheckoutState.Off]: {
            [CheckoutTransition.Start]: () => CheckoutState.Check,
            [CheckoutTransition.Cancel]: () => CheckoutState.Off,
        },
        [CheckoutState.Check]: {
            [CheckoutTransition.Cancel]: () => CheckoutState.Off,
            [CheckoutTransition.Cash]: () => CheckoutState.Calculator,
            [CheckoutTransition.Card]: () => CheckoutState.Confirm,
            [CheckoutTransition.Skip]: () => CheckoutState.Sending,
        },
        [CheckoutState.Calculator]: {
            [CheckoutTransition.Execute]: () => CheckoutState.Sending,
            [CheckoutTransition.Back]: () => CheckoutState.Check,
            [CheckoutTransition.Cancel]: () => CheckoutState.Off,
        },
        [CheckoutState.Confirm]: {
            [CheckoutTransition.Execute]: () => CheckoutState.Sending,
            [CheckoutTransition.Back]: () => CheckoutState.Check,
            [CheckoutTransition.Cancel]: () => CheckoutState.Off,
        },
        [CheckoutState.Sending]: {
            [CheckoutTransition.Success]: () => CheckoutState.Off,
            [CheckoutTransition.RetryableError]: () => CheckoutState.Off,
            [CheckoutTransition.Error]: () => CheckoutState.Failed,
        },
        [CheckoutState.Failed]: {
            [CheckoutTransition.Start]: () => CheckoutState.Check,
            [CheckoutTransition.Execute]: () => CheckoutState.Sending,
            [CheckoutTransition.Cancel]: () => CheckoutState.Off,
        },
    }

    public addCallback(state: StateChangeTrigger | Array<StateChangeTrigger> | null, callback: StateChangeCallback): CheckoutStateMachine {
        if (state === null) {
            this.anywayCallbacks.push(callback)
        } else if (typeof state === 'object') {
            for (const checkoutState of state) {
                this.callbacks[checkoutState].push(callback)
            }
        } else {
            this.callbacks[state].push(callback)
        }

        return this
    }

    public current(): Ref<CheckoutState> {
        return this.state
    }

    public currentState(): CheckoutState {
        return this.current().value
    }

    public dispatch(transition: CheckoutTransition, storage: any | null | undefined = undefined): void {
        const action: TransitionAction | undefined = this.transitions[this.state.value][transition]

        if (!action) {
            throw Error(`[CheckoutState] Invalid transition, current state: '${this.state.value}', transition: '${transition}'`)
        }

        const from = this.state

        // execute actual transition
        this.state.value = action()
        if (storage !== undefined) {
            this.storage = storage
        }

        const change: StateChange = {fromState: from.value, transition: transition, toState: this.state.value, storage: this.storage}

        this.callbacks[this.state.value].forEach(callback => callback(change))
        this.callbacks[transition].forEach(callback => callback(change))
        this.anywayCallbacks.forEach(callback => callback(change))
    }
}
