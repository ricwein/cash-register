<template>
  <div class="selection-view w-100 bg-dark">
    <div v-for="row in rows" class="d-flex row w-100 justify-content-start" :class="`row-cols-${gridWidthElements}`">
      <product-button v-for="[categoryName, product] in row" :product :categoryName :gridWidthElements @product-clicked="productClicked"></product-button>
    </div>
  </div>
</template>

<script setup lang="ts">
import ProductButton from "./product-button.vue";
import Category from "../../../model/category.ts"
import {computed, type PropType} from "vue";
import type Product from "../../../model/product.ts";

const emit = defineEmits(['product-clicked'])

const props = defineProps({
  categories: Object as PropType<Array<Category>>,
  displayHeightPortrait: String,
  historyHeightPortrait: String,
  gridWidthElements: Number,
})

const rows = computed((): Array<Array<[string, Product]>> => {
  let rows: Array<Array<[string, Product]>> = [];

  let products: Array<[string, Product]> = [];
  for (const category of props.categories ?? []) {
    for (const product of category.products) {
      if (products.length >= (props.gridWidthElements ?? 0)) {
        rows.push(products)
        products = [];
      }
      products.push([category.name, product])
    }
  }

  if (products.length > 0) {
    rows.push(products)
  }

  return rows
});

// bubble event to parent
function productClicked(product: Product) {
  emit('product-clicked', product)
}
</script>

<style scoped lang="scss">
.selection-view {
  min-height: calc(100vh - (v-bind(displayHeightPortrait) + v-bind(historyHeightPortrait)));
  height: 100%;
  overflow-y: scroll;
  max-height: 100vh;

  .row {
    margin-left: 0;
    margin-right: 0;
  }
}
</style>
