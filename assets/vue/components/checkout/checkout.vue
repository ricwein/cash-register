<template>
  <!-- choose payment-type dialog -->
  <v-dialog
      v-model="showDialogCheck"
      width="auto"
      min-width="50%"
      min-height="50%"
      persistent
      fullscreen
  >
    <v-card
        class="checkout-dialog"
        prepend-icon="mdi-cash-register"
        title="Zahlung bestätigen"
    >
      <div class="v-card-text">
        <p>Beleg bestätigen und zur Zahlung fortfahren?</p>
        <receipt :price :cart></receipt>
      </div>

      <template v-slot:actions>
        <div class="d-flex w-100 action-row">
          <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Cancel)" class="me-auto h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>

          <div v-if="price > 0.00">
            <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Card)" class="me-3 h-auto btn btn-primary py-3 px-5">
              <i class="fa-solid fa-xl fa-credit-card"></i>
              Karte
            </v-btn>

            <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Cash)" class="h-auto btn btn-success py-3 px-5">
              <i class="fa-solid fa-xl fa-money-bill-wave"></i>
              Bar
            </v-btn>
          </div>
          <div v-else-if="price < 0.00">
            <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Payout)" class="h-auto btn btn-success p-3">
              <i class="fa-xl mdi mdi-hand-coin"></i>
              Betrag Auszahlen
            </v-btn>
          </div>
          <div v-else>
            <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Continue)" class="h-auto btn btn-success p-3">
              <i class="fa-solid fa-xl fa-check"></i>
              Bestätigen
            </v-btn>
          </div>
        </div>
      </template>
    </v-card>
  </v-dialog>

  <!-- confirm payment-with-cash dialog -->
  <v-dialog
      v-model="showDialogCalculator"
      width="auto"
      min-width="50%"
      min-height="50%"
      persistent
      fullscreen
  >
    <v-card
        class="checkout-dialog"
        prepend-icon="mdi-cash-multiple"
        title="Barzahlung bestätigen"
    >
      <table class="table table-borderless align-middle">
        <tbody>
        <tr>
          <td class="text-right">zu Zahlen:</td>
          <th>
            <span class="font-monospace ms-2 fs-5">{{ NumberFormatter.format(price) }}</span>
          </th>
        </tr>
        <tr>
          <td class="text-right">Geld erhalten:</td>
          <th class="input-group input-group-lg border-bottom border-dark">
            <input
                id="calculatorInput"
                class="form-control border-0 font-monospace"
                type="number"
                min="0.0"
                step="0.01"
                @input="updateChangeMoney"
                @keyup.enter="checkoutStateMachine.dispatch(CheckoutTransition.Execute)"
                :placeholder="NumberFormatter.format(price).slice(0, -1).trimEnd()"
            />
            <span class="input-group-text border-0 bg-white">€</span>
          </th>
        </tr>
        <tr>
          <td class="text-right">Rückgeld:</td>
          <th class="fs-5">
            <input readonly disabled v-model="changeField" class="font-monospace text-black ms-2" type="text"/>
          </th>
        </tr>
        </tbody>
      </table>

      <template v-slot:actions>
        <div class="d-flex w-100 action-row">
          <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Cancel)" class="me-3 h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>

          <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Back)" class="me-auto h-auto btn btn-outline-primary p-3">
            <i class="fa-solid fa-xl fa-arrow-left"></i>
            Zurück
          </v-btn>

          <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Execute)" class="me-3 h-auto btn btn-success p-3">
            <i class="fa-solid fa-xl fa-check-double"></i>
            Zahlung Bestätigen
          </v-btn>
        </div>
      </template>
    </v-card>
  </v-dialog>

  <!-- confirm payment-with-card dialog -->
  <v-dialog
      v-model="showDialogConfirmation"
      width="auto"
      min-width="50%"
      min-height="50%"
      persistent
      fullscreen
  >
    <v-card
        class="checkout-dialog"
        prepend-icon="mdi-check-all"
        title="Kartenzahlung bestätigen"
    >
      <div class="v-card-text">
        <receipt :price :cart></receipt>
      </div>

      <template v-slot:actions>
        <div class="d-flex w-100 action-row">
          <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Cancel)" class="me-3 h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>

          <v-btn @click="checkoutStateMachine.dispatch(CheckoutTransition.Back)" class="me-auto h-auto btn btn-outline-primary p-3">
            <i class="fa-solid fa-xl fa-arrow-left"></i>
            Zurück
          </v-btn>

          <v-btn v-if="price > 0.0" @click="checkoutStateMachine.dispatch(CheckoutTransition.Execute)" class="me-3 h-auto btn btn-success p-3">
            <i class="fa-solid fa-xl fa-check-double"></i>
            Zahlung Bestätigen
          </v-btn>
          <v-btn v-else-if="price < 0.0" @click="checkoutStateMachine.dispatch(CheckoutTransition.Payout)" class="me-3 h-auto btn btn-success p-3">
            <i class="fa-xl mdi mdi-hand-coin"></i>
            Betrag Auszahlen
          </v-btn>
          <v-btn v-else @click="checkoutStateMachine.dispatch(CheckoutTransition.Continue)" class="me-3 h-auto btn btn-success p-3">
            <i class="fa-xl mdi mdi-check-all"></i>
            Bestätigen
          </v-btn>
        </div>
      </template>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import {computed, type PropType, ref} from "vue";
import {toast} from "vue3-toastify";
import {useSound} from "@vueuse/sound";
import Receipt from "./receipt.vue";
import {CheckoutState, CheckoutStateMachine, CheckoutTransition, type StateChange} from "../../../components/checkout-state-machine.ts";
import Product from "../../../model/product.ts";
import {NumberFormatter} from "../../../components/number-formatter.ts";
import Transactor from "../../../components/transactor.ts";
import {Transaction} from "../../../model/transaction.ts";
import deleteSound from '../../../sounds/delete.mp3'
import cancelSound from '../../../sounds/cancel.mp3'
import successSound from '../../../sounds/success.wav'
import warningSound from '../../../sounds/warning.wav'
import buttonSound from '../../../sounds/checkout.mp3'

const {play: playButton} = useSound(buttonSound)
const {play: playSuccess} = useSound(successSound)
const {play: playWarning} = useSound(warningSound)
const {play: playDelete} = useSound(deleteSound)
const {play: playCancel} = useSound(cancelSound)

const emit = defineEmits(['create-new-receipt', 'checkout-cancelled'])
const checkoutStateMachine = defineModel<CheckoutStateMachine>({required: true})

// setup vue component
const props = defineProps({
  price: {type: Number, required: true},
  cart: {type: Array<Product>, required: true},
  transactor: {type: Object as PropType<Transactor>, required: true},
  buttonSound: {type: Boolean, required: true},
  lazyCalculator: {type: Boolean, required: true},
})

// set up state-machine callbacks
checkoutStateMachine.value
    .addCallback(
        [CheckoutTransition.Card, CheckoutTransition.Cash, CheckoutTransition.Payout, CheckoutTransition.Continue],
        (change: StateChange): string => {
          if (change.transition === CheckoutTransition.Continue) return 'none'
          else if (change.transition === CheckoutTransition.Payout) return `${CheckoutTransition.Cash}`
          else return `${change.transition}`
        }
    )
    .addCallback(null, (): void => {
      changeField.value = '0,00'
    })
    .addCallback(null, (change: StateChange): void => {
      if (!props.buttonSound) return;

      if (change.transition === CheckoutTransition.Error || change.transition === CheckoutTransition.RetryableError) playWarning()
      else if (change.transition === CheckoutTransition.Success) playSuccess()
      else if (change.transition === CheckoutTransition.Cancel) playDelete()
      else if (change.transition === CheckoutTransition.Back) playCancel()
      else playButton()
    })
    .addCallback(CheckoutState.Sending, processPayment);

if (!props.lazyCalculator) {
  checkoutStateMachine.value
      .addCallback(CheckoutTransition.Cash, () => setTimeout(() => document.getElementById('calculatorInput')?.focus(), 200))
}

const changeField = ref<string>('0,00');

const showDialogCheck = computed<boolean>(() => checkoutStateMachine.value.currentState() === CheckoutState.Check)
const showDialogCalculator = computed<boolean>(() => checkoutStateMachine.value.currentState() === CheckoutState.Calculator)
const showDialogConfirmation = computed<boolean>(() => checkoutStateMachine.value.currentState() === CheckoutState.Confirm)

function updateChangeMoney(event: Event): void {
  const input: HTMLInputElement = event.target as HTMLInputElement;
  const value = Number(input.value);

  if (!value || isNaN(value)) {
    changeField.value = '–'
  } else {
    const returnMoney = value - props.price
    changeField.value = NumberFormatter.format(returnMoney)
  }
}

function processPayment(changes: StateChange): void {
  const paymentType: string = changes.storage ?? 'none'
  let cart: Record<number, number> = {};

  for (const product of props.cart) {
    if (!cart.hasOwnProperty(product.id)) {
      cart[product.id] = 0;
    }
    cart[product.id]++;
  }

  const transactor: Transactor = props.transactor
  transactor
      .send(new Transaction(paymentType, cart))
      .then(response => {
        if (response.state === CheckoutTransition.Success) {
          emit('create-new-receipt')
          toast.success(response.message, {autoClose: 750})
        } else if (response.state === CheckoutTransition.RetryableError) {
          emit('create-new-receipt')
          toast.warn(response.message, {autoClose: 1000})
        } else {
          toast.error(response.message)
        }

        checkoutStateMachine.value.dispatch(response.state)
      })
}
</script>

<style scoped lang="scss">
.checkout-dialog {
  *:has(> .action-row) {
    margin-top: auto !important;
  }
}
</style>
