<template>
  <div
      class="checkout-button bg-primary d-flex align-items-center flex-column justify-content-around"
      :class="{'disabled': cart.length <= 0}"
      @click="cart.length > 0 && $emit('registerConfirmed')"
  >
    <span class="fa-solid fa-cash-register fa-2xl mt-3"></span>
    <span>quittieren</span>
  </div>
</template>

<script setup lang="ts">
import type Product from "../../../../model/product.ts";

defineProps({
  cart: {type: Array<Product>, required: true},
})
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
