<template>
  <div>
    <div class="mb-4">
      <label for="title" class="form-label">
        Title
        <page-builder-tooltip>The title of the page displayed on the first heading.</page-builder-tooltip>
        <!-- <span data-tooltip title="The title of the page displayed on the first heading.">
            <i class="fa-solid fa-circle-info"></i>
        </span> -->
      </label>
      <input id="title" name="title" type="text" v-model="title" @keyup="titleKeyUp" @blur="slugifyTitle"
             class="form-input">
      <span v-if="titleError" class="form-error is-visible">{{ titleError }}</span>
    </div>
    <div class="mb-4">
      <label for="slug" class="form-label">
        Slug
        <page-builder-tooltip>The slug is used in the page URL, this is generated from the title but can also be manually edited.
        </page-builder-tooltip>
      </label>
      <div class="input-group">
        <div class="flex items-center">
          <input id="slug" name="slug" type="text" class="form-input" v-model="slug" @keyup="slugKeyUp"
                 @blur="slugifySlug">
        </div>
      </div>
      <span v-if="slugError" class="form-error is-visible">{{ slugError }}</span>
    </div>
  </div>
</template>
<script>
import _ from 'lodash';

export default {
  inject: ["bus"],

  props: {
    setTitle: String,
    titleError: String,
    setSlug: String,
    slugError: String
  },

  data() {
    return {
      title: this.setTitle,
      slug: this.setSlug
    }
  },

  methods: {
    slugifyTitle() {
      if (this.slug !== '') {
        return;
      }
      return this.slug = _.kebabCase(this.title);
    },
    slugifySlug() {
      return this.slug = _.kebabCase(this.slug);
    },
    titleKeyUp: _.debounce(function () {
      this.slugifyTitle();
      this.emitEvent();
    }, 1000),
    slugKeyUp: _.debounce(function () {
      this.slugifySlug();
      this.emitEvent();
    }, 1000),
    emitEvent() {
      this.bus.$emit('title-filled', {
        title: this.title,
        slug: this.slug,
      });
    }
  }
}
</script>
