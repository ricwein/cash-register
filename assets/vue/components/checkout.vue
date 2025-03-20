<template>
  <!-- choose payment-type dialog -->
  <v-dialog
      v-model="showDialogCheck"
      width="auto"
      min-width="50%"
      persistent
  >
    <v-card
        prepend-icon="mdi-cash-register"
        title="Zahlung bestätigen"
    >
      <div class="v-card-text">Beleg bestätigen und zur Zahlung fortfahren?</div>
      <h3 class="h3 text-center">{{ NumberFormatter.format(price) }}</h3>
      <template v-slot:actions>
        <div class="d-flex w-100">
          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Cash)" class="me-3 h-auto btn btn-outline-success p-3">
            <i class="fa-solid fa-xl fa-money-bill-wave"></i>
            Bar
          </v-btn>

          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Card)" class="h-auto btn btn-outline-success p-3">
            <i class="fa-solid fa-xl fa-credit-card"></i>
            Karte
          </v-btn>

          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Cancel)" class="ms-auto h-auto btn btn-danger p-3">
            Abbrechen
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
      persistent
  >
    <v-card
        prepend-icon="mdi-check-all"
        title="Kartenzahlung bestätigen"
    >
      <template v-slot:actions>
        <div class="d-flex w-100">
          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Execute)" class="me-3 h-auto btn btn-success p-3">
            <i class="fa-solid fa-xl fa-money-bill-wave"></i>
            Fortfahren
          </v-btn>

          <v-btn @click="checkoutState.dispatch(CheckoutTransition.Cancel)" class="ms-auto h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>
        </div>
      </template>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import {computed, ref} from "vue";
import {toast} from "vue3-toastify";
import {CheckoutState, CheckoutStateMachine, CheckoutTransition} from "../../components/checkout-state-machine.ts";
import Product from "../../model/product";
import {NumberFormatter} from "../../components/number-formatter.ts";

const paymentType = ref<string>('');

const emit = defineEmits(['create-new-receipt', 'checkout-cancelled'])
const checkoutState = defineModel<CheckoutStateMachine>({required: true})

// setup state-machine callbacks
checkoutState.value
    .addCallback(null, change => {
      if (change.transition === CheckoutTransition.Card) {
        paymentType.value = 'card'
      } else if (change.transition === CheckoutTransition.Cash) {
        paymentType.value = 'cash'
      }
    })
    .addCallback(CheckoutState.Sending, () => {
      processPayment()
    })
;

// setup vue component
const props = defineProps({
  price: {type: Number, required: true},
  cart: {type: Array<Product>, required: true},
  confirmEndpointUrl: {type: String, required: true},
})

const showDialogCheck = computed<boolean>(() => checkoutState.value.currentState() === CheckoutState.Check)
const showDialogCalculator = computed<boolean>(() => checkoutState.value.currentState() === CheckoutState.Calculator)
const showDialogConfirmation = computed<boolean>(() => checkoutState.value.currentState() === CheckoutState.Confirm)

function processPayment(): void {
  let data: Record<number, number> = {};

  for (const product of props.cart) {
    if (!data.hasOwnProperty(product.id)) {
      data[product.id] = 0;
    }
    data[product.id]++;
  }

  const endpointUrl = decodeURI(props.confirmEndpointUrl ?? '').replace("{{paymentType}}", paymentType.value)

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
