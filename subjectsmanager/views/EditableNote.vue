<template>
  <div
    contenteditable="true"
    @input="onInput"
    :placeholder="placeholderText"
  >
    {{ content }}
  </div>
</template>

<script>
export default {
  props: {
    content: {
      type: String,
      required: true
    },
    placeholderText: {
      type: String,
      default: 'Click to edit...'
    }
  },
  data() {
    return {
      localContent: this.content
    };
  },
  methods: {
    onInput(event) {

      this.localContent = event.target.innerText;
      
      this.$emit('input', this.localContent);
    }
  }
};
</script>