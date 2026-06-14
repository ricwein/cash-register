<template>
  <div class="history">
    <ul class="w-100 bg-light-subtle">
      <receipt-row
          v-for="(item, index) in cart.slice().reverse()"
          :item
          :selected="index === selectedIndex"
          @removeArticle="$emit('removeArticle', index)"
          @selectArticle="$emit('selectArticle', index)"
      ></receipt-row>
    </ul>
  </div>
</template>

<script setup lang="ts">
import type CartItem from "../../../model/cart-item.ts";
import type {PropType} from "vue";
import ReceiptRow from "./receipt-row.vue";

const props = defineProps({
  cart: {type: Object as PropType<Array<CartItem>>, required: true},
  numpadHeight: {type: String, default: '0px'},
  selectedIndex: {type: Number as PropType<number | null>, default: null},
})

defineEmits(['removeArticle', 'selectArticle'])
</script>

<style scoped lang="scss">
.history {
  background-color: var(--bs-white);
  height: calc(100vh - 10rem - v-bind('props.numpadHeight'));
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
