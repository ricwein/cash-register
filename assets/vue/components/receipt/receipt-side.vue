<template>
  <div class="history">
    <ul class="w-100 bg-light-subtle">
      <receipt-row v-for="(product, index) in products.slice().reverse()" :product @removeArticle="$emit('removeArticle', index)"></receipt-row>
    </ul>
  </div>
</template>

<script setup lang="ts">
import type Product from "../../../model/product.ts";
import type {PropType} from "vue";
import ReceiptRow from "./receipt-row.vue";

defineProps({
  products: {type: Object as PropType<Array<Product>>, required: true},
})
</script>

<style scoped lang="scss">
.history {
  height: calc(100vh - 10rem);
  border-top: 0.05em solid var(--bs-dark);
  overflow-y: scroll;

  ul {
    list-style: none;
    margin: 0;
    padding: 0;

    .price {
      font-family: var(--bs-font-monospace), monospace;
    }
  }
}
</style>
