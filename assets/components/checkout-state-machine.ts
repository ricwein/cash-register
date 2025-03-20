import {type Ref, ref} from 'vue'

export const enum CheckoutTransition {
    Start = 'start',
    Cancel = 'cancel',
    Cash = 'cash',
    Card = 'card',
    Skip = 'skip',
    Error = 'error',
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

export type StateChange = { fromState: CheckoutState, transition: CheckoutTransition, toState: CheckoutState }
type TransitionAction = (() => CheckoutState)
type StateChangeCallback = (change: StateChange) => void
type StateChangeTrigger = CheckoutTransition | CheckoutState

export class CheckoutStateMachine {
    private state = ref<CheckoutState>(CheckoutState.Off)
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
        [CheckoutTransition.Error]: [],
        [CheckoutTransition.Success]: [],
        [CheckoutTransition.Execute]: [],
    };
    private anywayCallbacks: StateChangeCallback[] = [];

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
            [CheckoutTransition.Cancel]: () => CheckoutState.Off,
        },
        [CheckoutState.Confirm]: {
            [CheckoutTransition.Execute]: () => CheckoutState.Sending,
            [CheckoutTransition.Cancel]: () => CheckoutState.Off,
        },
        [CheckoutState.Sending]: {
            [CheckoutTransition.Success]: () => CheckoutState.Off,
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

    public dispatch(transition: CheckoutTransition): void {
        const action: TransitionAction | undefined = this.transitions[this.state.value][transition]

        if (!action) {
            throw Error(`[CheckoutState] Invalid transition, current state: '${this.state.value}', transition: '${transition}'`)
        }

        const from = this.state

        // execute actual transition
        this.state.value = action()

        const change: StateChange = {fromState: from.value, transition: transition, toState: this.state.value}

        this.callbacks[this.state.value].forEach(callback => callback(change))
        this.callbacks[transition].forEach(callback => callback(change))
        this.anywayCallbacks.forEach(callback => callback(change))

        console.debug('[CheckoutState] transitioned', change)
    }
}
