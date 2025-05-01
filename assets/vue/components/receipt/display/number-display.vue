<template>
  <div class="w-100 bg-white display-container d-flex justify-content-between">
    <div v-if="transactionState.kind !== 'None'" class="transaction-info">
      <div class="badge font-monospace bg-warning shadow badge rounded-pill mt-1 ms-1">
        <span class="fa-solid fa-spinner fa-sm fa-pulse"></span>
        {{ transactionState.count }}
      </div>
    </div>
    <span class="display ms-auto">{{ NumberFormatter.format(price) }}</span>
    <span v-if="quickCheckout" class="d-flex justify-content-center">
      <checkout-button :buttonSound :cart @registerConfirmed="$emit('registerConfirmed', CheckoutTransition.Card)" :type="CheckoutTransition.Card"></checkout-button>
      <checkout-button :buttonSound :cart @registerConfirmed="$emit('registerConfirmed', CheckoutTransition.Cash)" :type="CheckoutTransition.Cash"></checkout-button>
    </span>
    <checkout-button v-else :buttonSound :cart @registerConfirmed="$emit('registerConfirmed', CheckoutTransition.Start)"></checkout-button>
  </div>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../../../components/number-formatter.ts";
import CheckoutButton from "../buttons/checkout-button.vue";
import type Product from "../../../../model/product.ts";
import type {TransactionState} from "../../../../components/transaction-state.ts";
import {CheckoutTransition} from "../../../../components/checkout-state-machine.ts";

defineProps({
  price: {type: Number, required: true},
  cart: {type: Array<Product>, required: true},
  buttonSound: {type: Boolean, required: true},
  quickCheckout: {type: Boolean, required: true},
  displayHeightPortrait: String,
})

defineEmits(['registerConfirmed'])
const transactionState = defineModel<TransactionState>({required: true})
</script>

<style scoped lang="scss">
.display-container {
  height: v-bind(displayHeightPortrait);
  text-align: right;

  .transaction-info {
    height: 100%;
  }

  .display {
    font-size: calc(v-bind(displayHeightPortrait) - 2rem);
    font-family: var(--bs-font-monospace), monospace;
    margin-left: 1em;
    margin-right: 1em;
  }
}
</style>
