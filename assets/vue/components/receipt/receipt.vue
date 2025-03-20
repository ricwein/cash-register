<template>
  <div class="history d-flex justify-content-between">
    <BackspaceButton class="sticky-top" @backspaceClicked="$emit('backspaceClicked')"></BackspaceButton>
    <ul class="w-100 bg-light-subtle">
      <receipt-row
          v-for="(product, index) in products.slice().reverse()"
          :product
          @removeArticle="$emit('removeArticle', index)"
      ></receipt-row>
    </ul>
  </div>
</template>

<script setup lang="ts">
import type Product from "../../../model/product.ts";
import type {PropType} from "vue";
import BackspaceButton from "./buttons/backspace-button.vue";
import ReceiptRow from "./receipt-row.vue";

defineProps({
  historyHeightPortrait: String,
  products: {type: Object as PropType<Array<Product>>, required: true},
})
</script>

<style scoped lang="scss">
.history {
  background-color: var(--bs-white);
  height: v-bind(historyHeightPortrait);
  border-top: 0.05em solid var(--bs-dark);
  border-bottom: 0.2rem solid var(--bs-dark);
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
