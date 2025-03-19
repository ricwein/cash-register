<template>
  <div class="selection-view w-100 bg-dark">
    <div class="d-flex flex-wrap align-items-baseline">
      <div v-for="row in products" class="row w-100 justify-content-between">
        <ProductButton v-for="[categoryName, product] in row" :product :categoryName :displayHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ProductButton from "./product-button.vue";
import Category from "../../model/category"
import {computed, type PropType} from "vue";
import type Product from "../../model/product.ts";

const emit = defineEmits(['product-clicked'])

const props = defineProps({
  categories: Object as PropType<Array<Category>>,
  displayHeightPortrait: String,
  historyHeightPortrait: String,
  gridWidthElements: Number,
  gridHeightElements: Number,
})

const products = computed((): Array<Array<[string, Product]>> => {
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
  border-top: 0.2rem solid var(--bs-dark);
  min-height: calc(100vh - (v-bind(displayHeightPortrait) + v-bind(historyHeightPortrait)));
}
</style>
