<template>
  <div class="selection-view w-100 bg-dark">
    <div class="d-flex flex-wrap align-items-baseline">
      <ProductButton v-for="[categoryName, product] in products" :product :categoryName :displayHeight :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductButton>
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
  displayHeight: String,
  historyHeight: String,
  gridWidthElements: Number,
  gridHeightElements: Number,
})

const products = computed((): Array<[string, Product]> => {
  let products: Array<[string, Product]> = [];
  for (const category of props.categories ?? []) {
    for (const product of category.products) {
      products.push([category.name, product])
    }
  }
  return products
});

// bubble event to parent
function productClicked(product: Product) {
  emit('product-clicked', product)
}
</script>

<style scoped lang="scss">
.selection-view {
  border-top: 0.2rem solid var(--bs-dark);
  min-height: calc(100vh - (v-bind(displayHeight) + v-bind(historyHeight)));
}
</style>
