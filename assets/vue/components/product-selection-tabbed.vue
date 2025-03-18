<template>
  <div class="selection-view w-100 bg-dark">
    <v-tabs-window v-model="tab">
      <v-tabs-window-item v-for="category in categories" :value="category.id">
        <div class="d-flex flex-wrap align-items-baseline">
          <ProductButton v-for="product in category.products" :category-name="category.name" :product :displayHeight :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductButton>
        </div>
      </v-tabs-window-item>
    </v-tabs-window>

    <v-card class="bg-dark sticky-bottom tab-bar w-100" variant="flat">
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
import Category from "../../model/category.ts";
import {computed, ref} from "vue";
import type Product from "../../model/product.ts";
import Color from "../../components/color.ts";

const emit = defineEmits(['product-clicked'])

const props = defineProps({
  categories: Array<Category>,
  displayHeight: String,
  historyHeight: String,
  gridWidthElements: Number,
  gridHeightElements: Number,
})

const tabBarHeight = 60;
const tabBarHeightSize = `${tabBarHeight}px`

const tab = ref(props.categories?.[0].id)

/**
 * bubble event to parent
 */
function productClicked(product: Product) {
  emit('product-clicked', product)
}
</script>

<style scoped lang="scss">
.selection-view {
  border-top: 0.2rem solid var(--bs-dark);
  min-height: calc(100vh - (v-bind(displayHeight) + v-bind(historyHeight) + v-bind(tabBarHeightSize)));
}

.tab-bar {
  border-top: 0.1rem solid var(--bs-dark);
  border-radius: 0;
  position: absolute;

  .tab:not(:first-of-type) {
    border-left: 0.1em solid rgba(255, 255, 255, 0.3);
  }
}
</style>
