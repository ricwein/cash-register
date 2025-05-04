<template>
  <div class="history d-flex justify-content-between">
    <backspace-button class="sticky-top" :useLandscapeMode :buttonSound @backspaceClicked="$emit('backspaceClicked')" @createNewReceipt="$emit('create-new-receipt')"></backspace-button>
    <ul class="w-100 bg-light-subtle">
      <receipt-row
          v-for="(product, index) in cart.slice().reverse()"
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
  cart: {type: Object as PropType<Array<Product>>, required: true},
  useLandscapeMode: {type: Boolean, required: true},
  buttonSound: {type: Boolean, required: true},
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
