<template>
    <div class="max-w-4xl mx-auto m-8 grid grid-cols-1 md:grid-cols-5">

      <Navigation class="col-span-1" />

      <div
        class="col-span-1 md:col-span-4 rounded border font-sans leading-loose text-lg text-gray-900 bg-white"
        :class="{ 'py-4' : ! $page.frontmatter.banner }"
      >

          <component :is="layout"></component>

      </div>
    </div>
</template>

<script>
import HomeLayout from './components/HomeLayout.vue';
import ChangelogLayout from './components/ChangelogLayout.vue';
import Navigation from './components/Navigation.vue';

export default {
  components: { HomeLayout, ChangelogLayout, Navigation },
  computed: {
    layout() {
      return this.$page.frontmatter.layout || 'HomeLayout';
    }
  }
}
</script>

<style lang="stylus">
  @import './styles/theme.styl';
</style>