<template>
  <div class="product col p-2 d-flex flex-column align-items-start" :style="{backgroundColor: product?.color}" @click="$emit('product-clicked', product)">
    <div class="product-category mb-1 small badge">{{ categoryName }}</div>
    <span class="product-icon mt-auto fa-2xl" v-if="product?.icon" :class="product?.icon"></span>
    <div class="product-price mt-auto font-monospace badge">{{ NumberFormatter.format(product?.price ?? 0.0) }}</div>
    <div class="product-name fw-medium h5 mb-0">{{ product?.name }}</div>
  </div>
</template>

<script setup lang="ts">
import {computed, type PropType} from "vue";
import {NumberFormatter} from "../../components/number-formatter";
import Product from "../../model/product";
import Color from "../../components/color.ts";

const props = defineProps({
  categoryName: String,
  product: Object as PropType<Product>,
  displayHeightPortrait: String,
  receiptWidthLandscape: String,
  gridWidthElements: Number,
  gridHeightElements: Number,
})

const color = computed(() => new Color(props.product?.color))
const peakColor = computed(() => color.value.getPeakColor())
const contrastColor = computed(() => color.value.getContrastColor())
const categoryTextColor = computed(() => color.value.isLight() ? 'var(--bs-secondary-color)' : 'var(--bs-secondary-bg)')
</script>

<style scoped lang="scss">
.product {
  outline: 0.1em solid var(--bs-dark);
  min-height: 9rem;
  border: 0.2em solid transparent;
  cursor: pointer;

  &:active, &:focus, &:hover {
    background-color: v-bind(peakColor) !important;
    border-color: var(--bs-primary);

    .product-price {
      outline: 1px solid grey;
    }
  }

  .product-category {
    color: v-bind(categoryTextColor);
  }

  .product-price, .product-name, .product-icon {
    color: v-bind(contrastColor);
  }

  .product-price {
    background-color: v-bind(peakColor);
    font-size: 1em;
  }

  > * {
    width: 100%;
    text-align: center;
  }
}
</style>
