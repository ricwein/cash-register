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
          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Cancel)" class="me-auto h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>

          <div v-if="price > 0.00">
            <v-btn @click="checkoutState.dispatch(CheckoutTransition.Cash, 'cash')" class="me-3 h-auto btn btn-success p-3">
              <i class="fa-solid fa-xl fa-money-bill-wave"></i>
              Bar
            </v-btn>

            <v-btn @click="checkoutState.dispatch(CheckoutTransition.Card, 'card')" class="h-auto btn btn-success p-3">
              <i class="fa-solid fa-xl fa-credit-card"></i>
              Karte
            </v-btn>
          </div>
          <div v-else-if="price < 0.00">
            <v-btn @click="checkoutState.dispatch(CheckoutTransition.Skip, 'cash')" class="h-auto btn btn-success p-3">
              <i class="fa-xl mdi mdi-hand-coin"></i>
              Betrag Auszahlen
            </v-btn>
          </div>
          <div v-else>
            <v-btn @click="checkoutState.dispatch(CheckoutTransition.Skip, 'none')" class="h-auto btn btn-success p-3">
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
          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Cancel)" class="me-3 h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>

          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Back)" class="me-auto h-auto btn btn-outline-primary p-3">
            <i class="fa-solid fa-xl fa-arrow-left"></i>
            Zurück
          </v-btn>

          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Execute)" class="me-3 h-auto btn btn-success p-3">
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
          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Cancel)" class="me-3 h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>

          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Back)" class="me-auto h-auto btn btn-outline-primary p-3">
            <i class="fa-solid fa-xl fa-arrow-left"></i>
            Zurück
          </v-btn>

          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Execute)" class="me-3 h-auto btn btn-success p-3">
            <i class="fa-solid fa-xl fa-check-double"></i>
            Zahlung Bestätigen
          </v-btn>
        </div>
      </template>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import {toast} from "vue3-toastify";
import {useSound} from "@vueuse/sound";
import Receipt from "./receipt.vue";
import {CheckoutState, CheckoutStateMachine, CheckoutTransition, type StateChange} from "../../../components/checkout-state-machine.ts";
import Product from "../../../model/product.ts";
import {NumberFormatter} from "../../../components/number-formatter.ts";
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
const checkoutState = defineModel<CheckoutStateMachine>({required: true})

// setup state-machine callbacks
checkoutState.value
    .addCallback(null, () => changeField.value = '0,00')
    .addCallback(null, change => {
      if (!props.buttonSound) {
        return;
      }

      if (change.transition === CheckoutTransition.Error) {
        playWarning()
      } else if (change.transition === CheckoutTransition.Success) {
        playSuccess()
      } else if (change.transition === CheckoutTransition.Cancel) {
        playDelete()
      } else if (change.transition === CheckoutTransition.Back) {
        playCancel()
      } else {
        playButton()
      }
    })
    .addCallback(CheckoutTransition.Cash, () => setTimeout(() => document.getElementById('calculatorInput')?.focus(), 200))
    .addCallback(CheckoutState.Sending, processPayment);

// setup vue component
const props = defineProps({
  price: {type: Number, required: true},
  cart: {type: Array<Product>, required: true},
  confirmEndpointUrl: {type: String, required: true},
  buttonSound: {type: Boolean, required: true},
})

const changeField = ref<string>('0,00');

const showDialogCheck = computed<boolean>(() => checkoutState.value.currentState() === CheckoutState.Check)
const showDialogCalculator = computed<boolean>(() => checkoutState.value.currentState() === CheckoutState.Calculator)
const showDialogConfirmation = computed<boolean>(() => checkoutState.value.currentState() === CheckoutState.Confirm)

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
  let data: Record<number, number> = {};

  for (const product of props.cart) {
    if (!data.hasOwnProperty(product.id)) {
      data[product.id] = 0;
    }
    data[product.id]++;
  }

  const endpointUrl = decodeURI(props.confirmEndpointUrl ?? '').replace("{{paymentType}}", paymentType)

  fetch(endpointUrl, {
    method: 'PUT',
    body: JSON.stringify(data)
  })
      .then(response => response.json())
      .then(response => {
        if (response.hasOwnProperty('success') && response.success) {
          emit('create-new-receipt')

          checkoutState.value.dispatch(CheckoutTransition.Success)

          toast.success('verarbeitet', {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
            autoClose: 1000,
          })
        } else if (response.hasOwnProperty('error')) {
          checkoutState.value.dispatch(CheckoutTransition.Error)

          toast.error(response.error, {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
          })
        }
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
