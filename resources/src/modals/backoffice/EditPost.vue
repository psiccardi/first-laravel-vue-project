<template>
    <b-modal
      :id="props.id || 'edit-user-modal'"
      ok-variant="dark"
      @ok="props.ok"
      scrollable
      :title="props.title || t('edit_post')"
    >
      <b-form :id="(props.id || 'edit-user-modal') + 'post_data'">
        <form-group-input
          :id="(props.id || 'edit-user-modal') + '-edit_post_title'"
          type="text"
          :label="t('title')"
          :description="t('backoffice.input.title.description')"
          :validFeedback="t('backoffice.input.title.valid_feedback')"
          :invalidFeedback="t('backoffice.input.title.invalid_feedback')"
          :inputValue="title_value"
          v-model="title_value"
        >
        </form-group-input>
        <form-group-textarea
          :id="(props.id || 'edit-user-modal') + '-edit_post_content'"
          type="text"
          :label="t('content')"
          :description="t('backoffice.input.content.description')"
          :validFeedback="t('backoffice.input.content.valid_feedback')"
          :invalidFeedback="t('backoffice.input.content.invalid_feedback')"
          :inputValue="content_value"
          v-model="content_value"
        >
        </form-group-textarea>
      </b-form>
    </b-modal>
  </template>

    <script setup>
  import { onMounted, ref, watch, defineModel } from "vue";
  import { useI18n } from "vue-i18n";
  const { t, locale } = useI18n();
  import FormGroupInput from "../../components/FormGroupInput.vue";
  import FormGroupTextarea from "../../components/FormGroupTextarea.vue";
  const title_value = ref("");
  const content_value = ref("");
  const emit = defineEmits([
    "edit-post-update-title",
    "edit-post-update-content",
  ]);

  console.log('locale', locale)

  const props = defineProps({
    ok: Function,
    edit_post: {
      type: Object,
      default: {}
    },
    id: String,
    title: String
  });

  const edit_post = ref({});

  // const edit_user = defineModel('edit_user');

  onMounted(() => {
      title_value.value = props.edit_post.title;
      content_value.value = props.edit_post.content;
      edit_post.value = props.edit_post;
  })

  const updateTitle = () => {
    console.log("edit-post-update-title", title_value.value);
    emit("edit-post-update-title", title_value.value);
  };
  const updateContent = () => {
    console.log("edit-post-update-content", content_value.value);
    emit("edit-post-update-content", content_value.value);
  };


  watch([title_value], function () {
    updateTitle();
  });

  watch([content_value], function () {
    updateContent();
  });
  </script>

    <style>
  </style>
