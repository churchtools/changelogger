<template>
    <div
      class="max-w-4xl mx-auto m-8 rounded border font-sans leading-loose text-lg text-gray-900 bg-white"
      :class="{ 'py-4' : ! $page.frontmatter.banner }"
    >

        <img
          v-if="$page.frontmatter.banner"
          :src="$page.frontmatter.banner"
          alt="Banner Image"
          class="rounded-t"
        />

        <Content class="px-8" />

    </div>
</template>

<style lang="stylus">
  @import './styles/theme.styl';
</style>