<template>
    <nav class="flex flex-row md:flex-col items-center items-stretch my-1 md:mt-8">
      <span
        v-for="site in $site.themeConfig.nav"
        class="m-2 mt-0 flex-1 md:flex-none"
      >
        <a
          :href="site.link"
          :title="site.text"
          class="flex items-center block w-full px-4 py-1 rounded font-medium text-gray-600 font-semibold hover:bg-gray-300 hover:text-gray-900"
          :class="activeClasses(site)"
        >
          <Icon
            :icon="site.icon"
            class="w-4 h-4"
          />
          <span class="ml-3">{{ site.text }}</span>
        </a>
      </span>
    </nav>
</template>

<script>
import Icon from './Icon.vue';

export default {
  components: { Icon },
  methods: {
    isActive(site) {
      return site.link === this.$page.path;
    },

    activeClasses(site) {
      if (! this.isActive(site)) {
        return {};
      }

      return [
        'text-gray-900'
      ]
    }
  }

}
</script>