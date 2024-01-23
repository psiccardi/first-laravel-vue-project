<template>
    <div class="d-flex justify-content-between filter-header" ref="postsFilter">
    <div>
        <b-button class="m-1" variant="dark" @click="openCreateModal">{{ t('create_post') }}</b-button>
    </div>
    <div>
      <div class="d-flex position-relative">
        <b-form-input class="m-1" id="title" v-model="title_filter" :placeholder="t('title')"></b-form-input>
        <autocomplete :placeholder="t('author')" v-on:update_autocomplete_value="updateFullNameValue" width="300px" :key="'full_name_filter_' + user.id" v-model="user_id" :input-value="full_name_filter" :input-value-callback="inputValueCallback"></autocomplete>
        <!-- <b-form-input class="m-1" id="full_name" v-model="full_name_filter" :placeholder="t('author')"></b-form-input> -->
        <!-- <b-form-input class="m-1" id="email_filter" v-model="email_filter" :placeholder="t('email')"></b-form-input> -->
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, watch, toRaw, onMounted, reactive } from "vue";
  import { useI18n } from "vue-i18n";
  import { useModal } from "bootstrap-vue-next";
  import {
    getAdminPostsAPI,
    getUsersAPI,
  } from "../../../../js/utilities/api.js";
  import * as db from 'lodash/debounce';
  import Autocomplete from "../../../components/backoffice/components/Autocomplete.vue";
  import Utils from "../../../../js/utilities/utils.js";
  const { t, locale } = useI18n();
  const props = defineProps({
    refreshUser: Function,
  });

  const emit = defineEmits(["update-user"]);
  const user = ref({});
  const postsFilter = ref(null);
  const posts = ref([]);
  const page = ref(0);
  const limit = ref(20);
  const title_filter = ref('');
  const full_name_filter = ref('');
  const user_id = ref('');
  const refreshUser = props.refreshUser;

  const resetData = () => {
    posts.value = [];
    page.value = 0;
  };

  const updateFullNameValue = function (value) {
    console.log('parent fullname', value);
    full_name_filter.value = value;
  }

  const debouncedFilterRequest = reactive({
    search: db((term) => {
        // email_filter.value = term;
        resetData();
        getPosts()
    }, 500),
    filterUsers: db((term) => {

    })
})


  const getPosts = () => {
    const data = {
        user_id: user_id.value,
        title: title_filter.value,
        page: page.value,
        limit: limit.value
    }
    getAdminPostsAPI(data, resp => {
        Utils.response.handleError(resp);
        const _posts = posts.value;
        _posts.push(...resp);
        posts.value = _posts;
    }, err => {
        Utils.DOM.toast(err.message, 'error', t);
        console.log(err.stack);
    })
  }

  watch([title_filter, user_id], () => {
    debouncedFilterRequest.search();
  })

  const inputValueCallback = function (value, cb) {
    getUsersAPI({
        full_name: value,
        page: 0,
        limit: 10
    }, resp => {
        Utils.response.handleError(resp);
        cb && cb(resp);
    }, err => {
        Utils.DOM.toast(err.message, 'error', t);
        console.log(err.stack);
    })
  }

  onMounted(() => {
    refreshUser(null, emit, user, {});
    getPosts();
  })
</script>

<style scoped>

#full_name_wrapper {
    top: 40px;
}

#full_name_wrapper ul {
    list-style-type: none;
    padding: 5px;
    border: 1px solid #828da0;
    background-color: white;
}

#full_name_wrapper ul li {
    background-color: white;
}
</style>
