<template>
  <li
      ref="articleRow"
      class="d-flex justify-content-between px-3 py-2"
      :class="{'mousedown': isMouseDown}"
  >
    <span>1<span class="fa-solid fa-times"></span></span>
    <div>
      <span :class="product.icon"></span>
      {{ product.name }}
    </div>
    <div class="price">
      {{ NumberFormatter.format(product.price) }}
    </div>
  </li>
</template>

<script setup lang="ts">
import {type PropType, ref, useTemplateRef} from 'vue'
import {onLongPress, useMousePressed} from '@vueuse/core'
import Product from "../../../model/product.ts";
import {NumberFormatter} from "../../../components/number-formatter.ts";

defineProps({
  product: {type: Object as PropType<Product>, required: true},
})

const emit = defineEmits(['removeArticle'])

const articleRow = useTemplateRef<HTMLLIElement>('articleRow')
const isMouseDown = ref<boolean>(false);
const longPressDelay: number = 500; // 0.5s
const longPressDelayWithUnit: string = `${longPressDelay}ms`

useMousePressed({
  target: articleRow,
  onPressed: () => isMouseDown.value = true,
  onReleased: () => isMouseDown.value = false,
})

onLongPress(articleRow, onLongPressCallbackHook, {
  modifiers: {prevent: false},
  delay: longPressDelay,
})

function onLongPressCallbackHook(): void {
  isMouseDown.value = false
  emit('removeArticle')
}
</script>

<style lang="scss" scoped>
@keyframes new-item {
  from {
    background-color: var(--bs-success);
  }

  to {
    background-color: var(--bs-success-bg-subtle);
  }
}

li {
  color: rgba(var(--bs-dark-rgb), 0.7);
  cursor: pointer;

  &.mousedown {
    transition: background-color v-bind(longPressDelayWithUnit) ease-in, padding v-bind(longPressDelayWithUnit) ease-in;
    background-color: var(--bs-danger) !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  &:hover {
    outline: 1px solid black;
    color: rgba(var(--bs-dark-rgb), 1.0);
    background-color: var(--bs-primary-bg-subtle);
  }

  &:not(:last-of-type) {
    border-bottom: 1px solid rgba(var(--bs-dark-rgb), 0.05);
  }

  &:nth-of-type(1) {
    color: rgba(var(--bs-dark-rgb), 1.0);
    animation: new-item 500ms 1 linear;

    &:not(:hover) {
      background-color: var(--bs-success-bg-subtle);
    }
  }

  &:nth-of-type(2) {
    color: rgba(var(--bs-dark-rgb), 0.95);
  }

  &:nth-of-type(3) {
    color: rgba(var(--bs-dark-rgb), 0.9);
  }

  &:nth-of-type(4) {
    color: rgba(var(--bs-dark-rgb), 0.85);
  }

  &:nth-of-type(5) {
    color: rgba(var(--bs-dark-rgb), 0.8);
  }

  &:nth-of-type(6) {
    color: rgba(var(--bs-dark-rgb), 0.75);
  }
}
</style>
