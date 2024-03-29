<template>
  <div class="pb-24">

    <button class="w-full btn btn-primary" type="button" @click="showModuleOptions = !showModuleOptions">
      <i class="mr-2 fa-solid fa-plus"></i>
      Add module
    </button>

    <div v-auto-animate="{ duration: 100 }" class="mb-4">
      <div v-if="showModuleOptions" id="add-module-menu" class="grid grid-cols-3 gap-4 p-4 bg-gray-100">
        <a v-for="(module, moduleName) in modules" @click="add(module, moduleName)" :key="module" class="flex-col w-full !py-4 btn btn-primary-outlined text-center">
          <p class="mb-1 text-2xl">
            <i :class="'fa-solid fa-' + module.icon"></i>
          </p>
          <p class="text-xs">
            {{ module.name }}
          </p>
        </a>
      </div>
    </div>

      <div>
        <draggable
            tag="ul"
            class="mt-4 content-list no-bullet"
            :list="content"
            v-bind="animate"
            @start="drag=true"
            @end="drag=false"
            handle=".handle"
            item-key="id"
            v-auto-animate="{ duration: 100 }"
        >
          <template #item="{element, index}">
            <li class="list-item" @click="component = element" :class="{ 'bg-gray-100': component === element }">
              <div class="w-full">
                <div class="flex justify-between">
                  <div class="">
                    <i class="mr-2 cursor-move fa-solid fa-grip-vertical handle"></i>
                    {{ element.name }}
                  </div>
                  <button type="button" @click="remove(index)">
                    <i class="transition-colors fa-regular fa-trash-can hover:text-error"></i>
                  </button>
                </div>

                <section class="bg-gray-100 rounded content-edit-module mt-4" v-if="component && component === element" :key="component.id">
                  <header class="z-20 flex justify-end pb-2 mb-4 border-b border-gray-400">
                    <div v-if="component.templates.length > 1" class="flex items-center space-x-4">
                      <span>Template:</span>
                      <select v-model="component.selected_template" @change="refreshPreview" class="form-input">
                        <option v-for="(template, index) in component.templates" :key="index" :value="template">
                          {{ template }}
                        </option>
                      </select>
                    </div>
                  </header>

                  <component
                      :is="component.module"
                      :key="component.id"
                      :id="component.id"
                      :fields="component.fields"
                      :page-model="pageModel"
                      :page-model-class="pageModelClass"
                      :media-upload-endpoint="mediaUploadEndpoint"
                      :get-folders-endpoint="getFoldersEndpoint"
                      :save-folder-endpoint="saveFolderEndpoint"
                      :gallery-media-upload-endpoint="galleryMediaUploadEndpoint"
                      :get-folder-endpoint="getFolderEndpoint"
                      :media-index-endpoint="mediaIndexEndpoint"
                      @update-content="update"
                  ></component>
                </section>

              </div>
            </li>
          </template>
        </draggable>
      </div>

      <section class="flex items-center justify-center w-full p-4 text-center border h-60 border-brand-almond-200" v-if="!component">
        <div>
          <h4>Click on a module's handle and drag to re-order</h4>
          <h6>Select a module to edit content</h6>
        </div>
      </section>
    
      <input type="hidden" name="content" :value="json()"/>
    </div>
</template>

<script>
import Draggable from 'vuedraggable';
import { v4 as uuidv4 } from 'uuid';
import debounce from 'lodash/debounce';
import _ from 'lodash';

export default {
  inject: ["bus"],
  components: {
    Draggable,
  },
  props: {
    modules: Object,
    pageContent: Array,
    defaultPageModel: Object,
    pageModelClass: String,
    mediaUploadEndpoint: String,
    getFoldersEndpoint: String,
    saveFolderEndpoint: String,
    galleryMediaUploadEndpoint: String,
    getFolderEndpoint: String,
    mediaIndexEndpoint: String,
  },
  data() {
    return {
      content: [],
      showModuleOptions: false,
      component: null,
      pageModel: this.defaultPageModel,
    }
  },
  created() {
    if (this.pageContent.length) {
      this.content = _.cloneDeep(this.pageContent);
    }
    this.bus.$on('set-page', (page) => {
      this.pageModel = page;
    })
  },
  computed: {
    animate() {
      return {
        animation: 200,
        disabled: false,
        ghostClass: 'ghost'
      }
    },
  },
  methods: {
    json(prop = 'content') {
      return JSON.stringify(this[prop]);
    },
    remove(index) {
      this.content.splice(index, 1);
      this.component = null;
    },
    add(module, name) {
      const component = {
        id: uuidv4(),
        module: name,
        show: true,
        ...module,
      };

      this.content = this.content.concat([component]);
      this.showModuleOptions = false;
      this.component = component;
    },
    update(payload) {
      this.debouncedRefreshPreview();
      const [uuid, prop, value] = payload;

      const i = _.findIndex(this.content, (module) => {
        return module.id === uuid;
      });

      if (i === -1) {
        return;
      }

      this.content[i].fields[prop] = {
        ...this.content[i].fields[prop],
        value,
      };
    },
    isEmpty(object) {
      return _.isEmpty(object);
    },
    debouncedRefreshPreview: debounce(function () {
      this.refreshPreview();
    }, 500),
    refreshPreview() {
      this.bus.$emit('refresh-preview', {});
    }
  }
}
</script>

<style lang="postcss" scoped>
.list-item {
  @apply flex items-center justify-between py-2 px-4 border border-gray-200 mb-4 rounded;
  @apply transition-colors;
}
</style>
