<template>
  <div class="selection-view w-100 bg-dark d-flex flex-column h-100">
    <v-tabs-window v-model="tab">
      <v-tabs-window-item v-for="[categoryId, categoryName, rows] in categoryRows" :value="categoryId">
        <div v-for="row in rows" class="row w-100 justify-content-start" :class="`row-cols-${gridWidthElements}`">
          <ProductButton v-for="product in row" :category-name="categoryName" :product :gridWidthElements @product-clicked="productClicked"></ProductButton>
        </div>
      </v-tabs-window-item>
    </v-tabs-window>

    <v-card class="bg-dark sticky-bottom tab-bar w-100 mt-auto" variant="flat">
      <v-tabs
          v-model="tab"
          align-tabs="center"
          fixed-tabs
          :height="tabBarHeight"
          slider-color="#fff"
      >
        <v-tab
            v-for="category in categories"
            class="tab"
            :style="{
              backgroundColor: category.color,
              color: (new Color(category.color)).getContrastColor()
            }"
            :key="category.id"
            :text="category.name"
            :value="category.id"
        ></v-tab>
      </v-tabs>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import ProductButton from "./product-button.vue";
import Category from "../../../model/category.ts";
import {computed, ref} from "vue";
import type Product from "../../../model/product.ts";
import Color from "../../../components/color.ts";

const emit = defineEmits(['product-clicked'])

const props = defineProps({
  categories: Array<Category>,
  displayHeightPortrait: String,
  historyHeightPortrait: String,
  gridWidthElements: Number,
})

const tabBarHeight = 60;

const categoryRows = computed((): Array<[number, string, Array<Array<Product>>]> => {
  let categories: Array<[number, string, Array<Array<Product>>]> = [];

  for (const category of props.categories ?? []) {
    let rows: Array<Array<Product>> = [];
    let products: Array<Product> = [];

    for (const product of category.products) {
      if (products.length >= (props.gridWidthElements ?? 0)) {
        rows.push(products)
        products = [];
      }

      products.push(product)
    }
    if (products.length > 0) {
      rows.push(products)
    }

    categories.push([category.id, category.name, rows]);
  }
  return categories
});

const tab = ref(props.categories?.[0].id)

// bubble event to parent
function productClicked(product: Product) {
  emit('product-clicked', product)
}
</script>

<style scoped lang="scss">
.selection-view {
  min-height: calc(100vh - (v-bind(displayHeightPortrait) + v-bind(historyHeightPortrait)));

  .row {
    margin-left: 0;
    margin-right: 0;
  }
}

.tab-bar {
  border-top: 0.1rem solid var(--bs-dark);
  border-radius: 0;
  position: relative;

  .tab:not(:first-of-type) {
    border-left: 0.1em solid rgba(255, 255, 255, 0.3);
  }
}
</style>
