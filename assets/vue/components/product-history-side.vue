<template>
  <div class="history d-flex justify-content-between">
    <div class="backspace-button d-flex align-items-center justify-content-around" @click="$emit('backspace-clicked')">
      <span class="fa-solid fa-2xl fa-backspace"></span>
    </div>
    <ul class="w-100 bg-light-subtle">
      <li v-for="(product, index) in products?.slice().reverse()" class="d-flex justify-content-between pe-3" :class="`item-${index}`">
        <span>1<span class="fa-solid fa-times"></span></span>
        <div>
          <span :class="product.icon"></span>
          {{ product.name }}
        </div>
        <div class="price">
          {{ NumberFormatter.format(product.price) }}
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../components/number-formatter";
import type Product from "../../model/product.ts";
import type {PropType} from "vue";

defineProps({
  products: Object as PropType<Array<Product>>,
})
</script>

<style scoped lang="scss">
.history {
  height: calc(100vh - 5rem);
  border-top: 0.05em solid var(--bs-dark);

  .backspace-button {
    background-color: var(--bs-dark-bg-subtle);
    height: 100% !important;
    width: 20vw;
    border-right: 0.05em solid var(--bs-dark);
    cursor: pointer;

    &:active, &:focus, &:hover {
      background-color: var(--bs-warning);

      * {
        color: var(--bs-danger);
      }
    }
  }

  ul {
    list-style: none;
    margin: 0;
    overflow: scroll;

    .price {
      font-family: var(--bs-font-monospace), monospace;
    }

    li {
      color: rgba(var(--bs-dark-rgb), 0.4);

      &.item-0 {
        color: rgba(var(--bs-dark-rgb), 1.0);
      }

      &.item-1 {
        color: rgba(var(--bs-dark-rgb), 0.9);
      }

      &.item-2 {
        color: rgba(var(--bs-dark-rgb), 0.8);
      }

      &.item-3 {
        color: rgba(var(--bs-dark-rgb), 0.7);
      }

      &.item-4 {
        color: rgba(var(--bs-dark-rgb), 0.6);
      }

      &.item-5 {
        color: rgba(var(--bs-dark-rgb), 0.5);
      }
    }
  }
}
</style>
