<template>
  <GenericModal
    key="confirm_delete_post_modal"
    id="confirm_delete_post_modal"
    :ok="(event) => deletePost(event, $bvModal)"
    :title="t('delete_post')"
    :body="t('backoffice.delete_post_confirm_question')"
  ></GenericModal>
  <edit-post
    :key="edit_post.id"
    id="edit_post_modal"
    :title="t('edit_post')"
    :edit_post="edit_post"
    v-on:edit-post-update-title="editPostUpdateTitle"
    v-on:edit-post-update-content="editPostUpdateContent"
    :ok="(event) => updatePost(event)"
  ></edit-post>
  <edit-post
    key="create_post"
    id="create_post_modal"
    :title="t('create_post')"
    v-on:edit-post-update-title="editPostUpdateTitle"
    v-on:edit-post-update-content="editPostUpdateContent"
    :ok="(event) => createPost(event)"
  ></edit-post>
  <div class="d-flex justify-content-between filter-header" ref="postsFilter">
    <div>
      <b-button class="m-1" variant="dark" @click="fnShowCreatePostModal">{{
        t("create_post")
      }}</b-button>
    </div>
    <div>
      <div class="d-flex position-relative">
        <b-form-input
          class="m-1"
          id="title"
          v-model="title_filter"
          :placeholder="t('title')"
        ></b-form-input>
        <autocomplete v-if="user.role?.name == 'administrator'"
          :placeholder="t('author')"
          v-on:update_autocomplete_value="updateFullNameValue"
          width="300px"
          :key="'full_name_filter_' + user.id"
          v-model="user_id"
          :input-value="full_name_filter"
          :input-value-callback="inputValueCallback"
        ></autocomplete>
        <!-- <b-form-input class="m-1" id="full_name" v-model="full_name_filter" :placeholder="t('author')"></b-form-input> -->
        <!-- <b-form-input class="m-1" id="email_filter" v-model="email_filter" :placeholder="t('email')"></b-form-input> -->
      </div>
    </div>
  </div>
  <div ref="postsList" class="infinite-scroll posts_wrapper" id="posts_wrapper">
    <post v-for="post in posts" :delete-click="() => openConfirmDeletePostModal(post)" :edit-click="() => openEditPostModal(post)" :post="post" :key="'posts' + post.id">
    </post>
  </div>
</template>

<script setup>
import { ref, watch, toRaw, onMounted, reactive } from "vue";
import { useI18n } from "vue-i18n";
import { useModal } from "bootstrap-vue-next";
import {
  createPostAPI,
  editPostAPI,
  getAdminPostsAPI,
  getOperatorPostsAPI,
  getUsersAPI,
  deletePostAPI
} from "../../../js/utilities/api.js";
import * as db from "lodash/debounce";
import GenericModal from "../../modals/GenericModal.vue";
import Autocomplete from "../../components/backoffice/Autocomplete.vue";
import EditPost from "../../modals/backoffice/EditPost.vue";
import Post from "../../components/backoffice/Post.vue";
import Utils from "../../../js/utilities/utils.js";
const { t, locale } = useI18n();
const props = defineProps({
  refreshUser: Function,
});

const { show: showCreatePostModal, hide: hideCreatePostModal } =
  useModal("create_post_modal");

const fnShowCreatePostModal = () => {
  edit_post.value = {};
  showCreatePostModal();
};

const { show: showEditPostModal, hide: hideEditPostModal } = useModal('edit_post_modal');
const { show: showConfirmDeletePostModal, hide: hideConfirmDeletePostModal} = useModal('confirm_delete_post_modal');

const openEditPostModal = (post) => {
  edit_post.value = JSON.parse(JSON.stringify(post));
  showEditPostModal();
}

const openConfirmDeletePostModal = (post) => {
    showConfirmDeletePostModal();
    edit_post.value = post;
}

const emit = defineEmits(["update-user"]);
const user = ref({});
const postsFilter = ref(null);
const posts = ref([]);
const edit_post = ref({});
const page = ref(0);
const limit = ref(20);
const title_filter = ref("");
const full_name_filter = ref("");
const user_id = ref("");
const refreshUser = props.refreshUser;
const postsList = ref(null);
const data_finished = ref(false);
const fetching_posts_data = ref(false);

const resetData = () => {
  posts.value = [];
  page.value = 0;
  data_finished.value = false;
};

const deletePost = (e) => {
  e.preventDefault();
  const data = {
    id: edit_post.value.id
  }

  deletePostAPI(data, resp => {
    Utils.response.handleError(resp);
    updatePostInsideList(resp, true);
    hideConfirmDeletePostModal();
  }, err => {
    Utils.DOM.toast(err.message);
    console.log(err.stack);
  })
}

const updatePostInsideList = (editedPost, remove = false) => {
    const _posts = posts.value;
    let index = -1;
    _posts.forEach((u, i) => {
        if (u.id == editedPost.id) {
            _posts[i] = editedPost;
            index = i;
        }
    })
    if (remove) {
        _posts.splice(index, 1);
    }
    posts.value = _posts;
}

const updatePost = (e) => {
    e.preventDefault();
    console.log(edit_post.value);
    if (!edit_post.value.title || !edit_post.value.content) {
        Utils.DOM.toast(t("fill_all_fields"), "error", t);
        return;
    }
    editPostAPI(
        edit_post.value,
        (resp) => {
            Utils.response.handleError(resp, t)
            updatePostInsideList(resp, false);
            // const index = posts.value.findIndex((v, i) => {
            //     return v.id == resp.id;
            // })
            // const _posts = Array.from(posts.value);
            // if (index !== -1) {
            //     _posts[index] = resp;
            // }
            // posts.value = _posts;
            hideEditPostModal();

        },
        err => {
            Utils.DOM.toast(err.message,'error', t);
            console.log(err.stack);
        }
    )
}

const createPost = (e) => {
  e.preventDefault();
  console.log(edit_post.value);
  if (!edit_post.value.title || !edit_post.value.content) {
    Utils.DOM.toast(t("fill_all_fields"), "error", t);
    return;
  }

  createPostAPI(
    edit_post.value,
    (resp) => {
      Utils.response.handleError(resp, t);
      const _post = [resp];
      _post.push(...posts.value);
      console.log(_post);
      hideCreatePostModal();
      posts.value = _post;
    },
    (err) => {
      Utils.DOM.toast(err.message,'error', t);
      console.log(err.stack);
    }
  );
};

const editPostUpdateTitle = function (value) {
  console.log("parent update-title", value);
  edit_post.value.title = value;
};

const editPostUpdateContent = function (value) {
  console.log("parent update-content", value);
  edit_post.value.content = value;
};

const updateFullNameValue = function (value) {
  console.log("parent fullname", value);
  full_name_filter.value = value;
};

const debouncedFilterRequest = reactive({
  search: db((term) => {
    // email_filter.value = term;
    resetData();
    getPosts();
  }, 500),
  filterUsers: db((term) => {}),
});

const getPosts = () => {
  const data = {
    user_id: user_id.value,
    title: title_filter.value,
    page: page.value,
    limit: limit.value,
  };
  fetching_posts_data.value = true;
  const fn = user.value.role?.name == 'administrator' ? getAdminPostsAPI : getOperatorPostsAPI;

  fn(
    data,
    (resp) => {
      fetching_posts_data.value = false;
      Utils.response.handleError(resp);
      const _posts = posts.value;
      _posts.push(...resp);
      posts.value = _posts;
      if (resp.length === 0) {
        data_finished.value = true;
      }
    },
    (err) => {
      fetching_posts_data.value = false;
      Utils.DOM.toast(err.message, "error", t);
      console.log(err.stack);
    }
  );
};

watch([full_name_filter], () => {
    if (full_name_filter.value == '') {
        user_id.value = null;
    }
});

watch([title_filter, user_id], () => {
  debouncedFilterRequest.search();
});

const inputValueCallback = function (value, cb) {
  getUsersAPI(
    {
      full_name: value,
      page: 0,
      limit: 10,
    },
    (resp) => {
      Utils.response.handleError(resp);
      cb && cb(resp);
    },
    (err) => {
      Utils.DOM.toast(err.message, "error", t);
      console.log(err.stack);
    }
  );
};

onMounted(() => {
  refreshUser(null, emit, user, {}, function () {
    getPosts();
  });

  Utils.functions.initInfiniteScroll(postsList.value, () => {
    console.log('scrolling');
    if (data_finished.value == false && !fetching_posts_data.value) {
      page.value += 1;
      getPosts();
    }
  });
});
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

.posts_wrapper {
  max-height: calc(100vh - 120px); /* -40px -40px -40px */
  overflow: auto;
  padding: 20px;
}
</style>
