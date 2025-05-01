<template>
  <div
      v-if="type === CheckoutTransition.Card"
      class="checkout-button bg-primary d-flex align-items-center flex-column justify-content-around"
      :class="{'disabled': cart.length <= 0}"
      @click="click"
  >
    <span class="fa-solid fa-credit-card fa-2xl mt-3"></span>
    <span>Karte</span>
  </div>
  <div
      v-else-if="type === CheckoutTransition.Cash"
      class="checkout-button bg-success d-flex align-items-center flex-column justify-content-around"
      :class="{'disabled': cart.length <= 0}"
      @click="click"
  >
    <span class="fa-solid fa-money-bill-wave fa-2xl mt-3"></span>
    <span>Bar</span>
  </div>
  <div
      v-else
      class="checkout-button bg-primary d-flex align-items-center flex-column justify-content-around"
      :class="{'disabled': cart.length <= 0}"
      @click="click"
  >
    <span class="fa-solid fa-cash-register fa-2xl mt-3"></span>
    <span>quittieren</span>
  </div>
</template>

<script setup lang="ts">
import type Product from "../../../../model/product.ts";
import {useSound} from "@vueuse/sound";
import buttonSound from '../../../../sounds/checkout.mp3'
import {CheckoutTransition} from "../../../../components/checkout-state-machine.ts";
import type {PropType} from "vue";

const {play} = useSound(buttonSound)
const emit = defineEmits(['registerConfirmed'])

const props = defineProps({
  cart: {type: Array<Product>, required: true},
  buttonSound: {type: Boolean, required: true},
  type: {type: Object as PropType<CheckoutTransition>, default: CheckoutTransition.Start}
})

function click() {
  if (props.cart?.length <= 0) {
    return
  }

  if (props.buttonSound) {
    play()
  }

  emit('registerConfirmed')
}
</script>

<style scoped lang="scss">
.checkout-button {
  border-left: 0.05em solid var(--bs-dark);
  width: 10vw;

  &:not(.disabled) {
    cursor: pointer;

    &:active, &:focus, &:hover {
      background-color: var(--bs-info) !important;
    }
  }

  &.disabled {
    cursor: not-allowed;
    background-color: var(--bs-secondary) !important;
  }
}
</style>
