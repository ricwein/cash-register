<template>
  <div class="selection-view w-100 d-flex flex-column h-100">
    <v-tabs-window v-model="tab">
      <v-tabs-window-item v-for="[categoryId, categoryName, rows] in categoryRows" :value="categoryId">
        <div v-for="row in rows" class="row w-100 justify-content-start" :class="`row-cols-${gridWidthElements}`">
          <product-button v-for="product in row" :buttonSound :show-category="false" :category-name="categoryName" :product :gridWidthElements @product-clicked="productClicked"></product-button>
        </div>
      </v-tabs-window-item>
    </v-tabs-window>

    <v-card class="bg-dark tab-bar w-100 mt-auto" variant="flat">
      <v-tabs
          v-model="tab"
          :height="tabBarHeight"
          slider-color="#fff"
      >
        <v-tab
            v-for="category in categories"
            class="tab flex-fill"
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
  categories: {type: Array<Category>, required: true},
  displayHeightPortrait: String,
  historyHeightPortrait: String,
  gridWidthElements: Number,
  buttonSound: {type: Boolean, required: true},
})

const tabBarHeight = 60;

const categoryRows = computed((): Array<[number, string, Array<Array<Product>>]> => {
  let categories: Array<[number, string, Array<Array<Product>>]> = [];

  for (const category of props.categories) {
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

const tab = ref<number>(props.categories[0].id)

// bubble event to parent
function productClicked(product: Product) {
  emit('product-clicked', product)
}
</script>

<style scoped lang="scss">
.selection-view {
  max-height: 100vh;
  overflow-y: scroll;

  .row {
    margin-left: 0;
    margin-right: 0;
  }
}

.tab-bar {
  border-top: 0.1rem solid var(--bs-dark);
  border-radius: 0;
  position: sticky;
  bottom: 0;

  .tab:not(:first-of-type) {
    border-left: 0.1em solid rgba(255, 255, 255, 0.3);
  }

  *:has(> .mdi-chevron-left),
  *:has(> .mdi-chevron-right) {
    background-color: var(--bs-dark-bg-subtle) !important;
  }

  :has(> .mdi-chevron-left) {
    border-right: 1px solid var(--bs-dark);
  }

  :has(> .mdi-chevron-right) {
    border-left: 1px solid var(--bs-dark);
  }

  *:has(> .tab) {
    flex-wrap: wrap !important;
  }
}
</style>
