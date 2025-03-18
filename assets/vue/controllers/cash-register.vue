<template>
  <div class="sticky-top">
    <NumberDisplay :price :displayHeight @register-confirmed="registerConfirmed"></NumberDisplay>
    <ProductHistory :products="cart" :historyHeight @backspace-clicked="backspaceClicked"></ProductHistory>
  </div>
  <ProductSelectionTabbed v-if="useCategoryTabs" :categories :displayHeight :historyHeight :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelectionTabbed>
  <ProductSelection v-else :categories :displayHeight :historyHeight :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelection>
</template>

<script setup lang="ts">
import {type PropType, ref} from "vue";
import NumberDisplay from "../components/number-display.vue";
import ProductSelection from "../components/product-selection.vue";
import ProductHistory from "../components/product-history.vue";
import {toast} from "vue3-toastify";
import ProductSelectionTabbed from "../components/product-selection-tabbed.vue";
import Category from "../../model/category";
import Product from "../../model/product";

const props = defineProps({
  confirmEndpointUrl: String,
  startPrice: Number,
  categories: Object as PropType<Array<Category>>,
  gridWidthElements: Number,
  gridHeightElements: Number,
  useCategoryTabs: Boolean
});

const price = ref<Number>(props.startPrice)
const cart = ref<Product[]>([]);
const displayHeight = '5rem'
const historyHeight = '7rem'

function productClicked(product: Product): void {
  cart.value.push(product)
  _recalculateCart()
}

function backspaceClicked(): void {
  cart.value.pop()
  _recalculateCart()
}

function _recalculateCart(): void {
  price.value = cart.value
      .map((product: Product) => product.price)
      .reduce((price: number, prev: number): number => price + prev, 0.0)
}

function registerConfirmed(): void {
  let data: Array<number, number> = {};

  /** @var {Product} product */
  for (const product of cart.value) {
    if (!data.hasOwnProperty(product.id)) {
      data[product.id] = 0;
    }
    data[product.id]++;
  }

  fetch(props.confirmEndpointUrl, {
    method: 'PUT',
    body: JSON.stringify(data)
  })
      .then(response => response.json())
      .then(response => {
        if (response.hasOwnProperty('success') && response.success) {
          cart.value = [];
          _recalculateCart()

          toast.success('verarbeitet', {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
            autoClose: 1000,
          })
        } else if (response.hasOwnProperty('error')) {
          toast.error(response.error, {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
          })
        }
      })
}
</script>

<style lang="scss">
body {
  user-select: none !important;
}
</style>
