<template v-model="showCheckout">
  <v-dialog
      v-model="showPaymentSelection"
      width="auto"
      min-width="50%"
      persistent
  >
    <v-card
        prepend-icon="fa-solid fa-money-bill-transfer"
        title="Zahlung bestätigen"
        text="Beleg bestätigen und zur Zahlung fortfahren?"
    >
      <template v-slot:actions>
        <div class="d-flex w-100">
          <v-btn @click="selectPaymentType('cash')" class="me-3 h-auto btn btn-outline-success p-3">
            <i class="fa-solid fa-xl fa-money-bill-wave"></i>
            Bar
          </v-btn>

          <v-btn @click="selectPaymentType('card')" class="h-auto btn btn-outline-success p-3">
            <i class="fa-solid fa-xl fa-credit-card"></i>
            Karte
          </v-btn>

          <v-btn @click="$emit('checkout-cancelled')" class="ms-auto h-auto btn btn-danger p-3">
            Abbrechen
          </v-btn>
        </div>
      </template>
      <h3 class="h3 text-center">{{ NumberFormatter.format(price) }}</h3>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import {ref} from "vue";
import {toast} from "vue3-toastify";
import Product from "../../model/product";
import {NumberFormatter} from "../../components/number-formatter.ts";

const props = defineProps({
  price: {type: Number, required: true},
  cart: {type: Array<Product>, required: true},
  showCheckout: {type: Boolean, default: false},
  confirmEndpointUrl: {type: String, required: true},
})

const emit = defineEmits(['create-new-receipt', 'checkout-cancelled'])

const showPaymentSelection = ref<boolean>(false);
const paymentType = ref<string>('');

function selectPaymentType(type: string): void {
  paymentType.value = type;
  showPaymentSelection.value = false;
}

function processPayment(): void {
  let data: Record<number, number> = {};

  for (const product of props.cart) {
    if (!data.hasOwnProperty(product.id)) {
      data[product.id] = 0;
    }
    data[product.id]++;
  }

  const endpointUrl = props.confirmEndpointUrl.replace("{{paymentType}}", paymentType.value)

  fetch(endpointUrl, {
    method: 'PUT',
    body: JSON.stringify(data)
  })
      .then(response => response.json())
      .then(response => {
        if (response.hasOwnProperty('success') && response.success) {
          emit('create-new-receipt')

          toast.success('verarbeitet', {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
            autoClose: 1000,
          })
        } else if (response.hasOwnProperty('error')) {
          toast.error(response.error, {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
          })
        }
      })
}
</script>
