<template>
  <div>
    <img
      v-if="$page.frontmatter.banner"
      :src="$page.frontmatter.banner"
      alt="Banner Image"
      class="rounded-t"
    />

    <Content class="home-layout px-8" />
  </div>
</template>

<script>
export default {
  name: 'HomeLayout'
}
</script>