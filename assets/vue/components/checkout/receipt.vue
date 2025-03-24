<template>
  <div class="bg-dark-subtle overflow-y-scroll receipt rounded p-3 shadow">
    <table class="table table-borderless table-sm">
      <tbody>
      <tr v-for="article in groupedArticles">
        <td class="bg-dark-subtle">{{ article[0] }}<span class="fa-solid fa-times"></span></td>
        <td class="bg-dark-subtle"><span :class="article[1].icon"></span> {{ article[1].name }}</td>
        <td class="bg-dark-subtle">{{ NumberFormatter.format(article[0] * article[1].price) }}</td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" class="bg-dark-subtle text-right">∑</td>
        <th class="bg-dark-subtle fw-bold border-top border-dark" ref="priceSum">{{ NumberFormatter.format(price) }}</th>
      </tr>
      </tfoot>
    </table>
  </div>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {templateRef} from "@vueuse/core";
import type Product from "../../../model/product.ts";
import {NumberFormatter} from "../../../components/number-formatter.ts";

const props = defineProps({
  price: {type: Number, required: true},
  cart: {type: Array<Product>, required: true},
})
const priceSum = templateRef('priceSum')

type ArticleList = Record<string, [number, Product]>
const groupedArticles = computed<ArticleList>(() => {
  let articles: ArticleList = {}

  /** @var {Product} article */
  for (const article of props.cart) {
    if (articles.hasOwnProperty(article.name)) {
      articles[article.name][0]++
    } else {
      articles[article.name] = [1, article]
    }
  }
  return articles
})

setTimeout(() => priceSum.value.scrollIntoView({behavior: 'smooth'}), 200)
</script>

<style scoped lang="scss">
.receipt {
  max-height: calc(100vh - 205px);
}
</style>
