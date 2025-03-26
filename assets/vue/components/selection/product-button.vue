<template>
  <div
      v-if="product.imageUrl !== null"
      class="product product-image col p-2 d-flex flex-column align-items-start"
      :style="{backgroundColor: product.color, backgroundImage: `url(${product.imageUrl})`}"
      @click="buttonClick"
  >
    <div v-if="showCategory" class="product-category mb-1 small badge">{{ categoryName }}</div>

    <div class="product-price mt-auto font-monospace badge">{{ NumberFormatter.format(product.price) }}</div>
    <div class="product-name fw-medium h5 mb-0">{{ product.name }}</div>
  </div>

  <div
      v-else
      class="product col p-2 d-flex flex-column align-items-start"
      :style="{backgroundColor: product.color}"
      @click="buttonClick"
  >
    <div v-if="showCategory" class="product-category mb-1 small badge">{{ categoryName }}</div>

    <span v-if="showCategory && product.icon !== null" class="product-icon mt-auto fa-2xl" :class="product.icon"></span>
    <span v-else-if="product.icon !== null" class="product-icon mt-5 fa-2xl" v-if="product.icon" :class="product.icon"></span>

    <div class="product-price mt-auto font-monospace badge">{{ NumberFormatter.format(product.price) }}</div>
    <div class="product-name  fw-medium h5 mb-0">{{ product.name }}</div>
  </div>
</template>

<script setup lang="ts">
import {computed, type PropType} from "vue";
import {useVibrate} from '@vueuse/core';
import {NumberFormatter} from "../../../components/number-formatter.ts";
import Product from "../../../model/product.ts";
import Color from "../../../components/color.ts";
import {useSound} from '@vueuse/sound'
import buttonSound from '../../../sounds/article.mp3'

const {play} = useSound(buttonSound)

const props = defineProps({
  categoryName: String,
  product: {type: Object as PropType<Product>, required: true},
  gridWidthElements: Number,
  showCategory: Boolean,
  buttonSound: {type: Boolean, required: true},
})

const color = computed(() => new Color(props.product.color))
const peakColor = computed(() => color.value.getPeakColor())
const contrastColor = computed(() => color.value.getContrastColor())
const categoryTextColor = computed(() => color.value.isLight() ? 'var(--bs-secondary-color)' : 'var(--bs-secondary-bg)')

const emit = defineEmits(['product-clicked'])

function buttonClick() {
  useVibrate({pattern: 100})
  if (props.buttonSound) {
    play()
  }
  emit('product-clicked', props.product)
}
</script>

<style scoped lang="scss">
.product {
  outline: 0.1em solid var(--bs-dark);
  min-height: 12rem;
  border: 0.2em solid transparent;
  cursor: pointer;

  &.product-image {
    background-size: cover;
    background-position: center;

    .product-category, .product-name {
      border-radius: 0.2em;
      background-color: v-bind(categoryTextColor);
      color: v-bind(peakColor);
    }
  }

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

  .product-name {
    font-size: calc(0.4rem + 0.8vw);
    text-overflow: ellipsis;
    overflow: hidden;
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
