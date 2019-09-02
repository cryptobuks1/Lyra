<template>
  <div class="pt-3 h-100">
    <lyra-loader v-if="isLoaderEnabled" class="py-5"></lyra-loader>
    <template v-else>
      <div class="row m-0">
        <div v-for="element in elements" class="element" :title="element.name"
             @contextmenu.prevent="openContextElement($event, element)">

          <template v-if="/^image\/\w+$/.test(element.mime)">
            <image-file :element="element"></image-file>
          </template>

          <template v-else-if="/^video\/\w+$/.test(element.mime)">
            <video-file :element="element"></video-file>
          </template>

          <template v-else-if="/^audio\/\w+$/.test(element.mime)">
            <audio-file :element="element"></audio-file>
          </template>

          <template v-else-if="/^directory$/.test(element.mime)">
            <directory :element="element"></directory>
          </template>

          <template v-else>
            <generic-file :element="element"></generic-file>
          </template>

        </div>
      </div>

      <div class="h-100 w-100 media-backdrop" @contextmenu.prevent="openContextGeneric($event)"></div>

      <preview-modal :element="previewElement" ref="previewModal"
                     :key="previewElement.path" v-if="previewElement"></preview-modal>

      <context-menu-elements :element="contextElement" :folder-tree="folderTree"
                             @reload-viewer="loadViewer"></context-menu-elements>
      <context-menu-generic></context-menu-generic>

    </template>
  </div>
</template>

<script>
  import ImageFile from './FileTypes/ImageFile'
  import VideoFile from './FileTypes/VideoFile'
  import AudioFile from './FileTypes/AudioFile'
  import GenericFile from './FileTypes/GenericFile'
  import Directory from './FileTypes/Directory'

  import PreviewModal from './Modals/PreviewModal'
  import DetailsModal from './Modals/DetailsModal'

  import ContextMenuElements from './ContextMenu/ContextMenuElements'
  import ContextMenuGeneric from './ContextMenu/ContextMenuGeneric'

  export default {
    components: {
      PreviewModal, DetailsModal,
      ContextMenuElements, ContextMenuGeneric,
      ImageFile, VideoFile, AudioFile, GenericFile, Directory
    },
    props: ['folderTree'],
    data() {
      return {
        elements: null,
        previewElement: null,
        contextElement: null,
        contextMenu: null,
      }
    },
    methods: {
      getElementsFolder: function () {
        const path = this.$route.fullPath.split('?')[0];
        const query = this.$route.fullPath.split('?')[1];
        this.$http.get(`${path}/files?${query}`).then((response) => this.elements = response.data);
      },
      openContextElement: function (e, element) {
        this.removePreviousContextMenu();
        this.contextMenu = $('#contextMenuElement');
        this.contextElement = element;
        this.mountContextMenu(e);
      },
      openContextGeneric: function (e) {
        this.removePreviousContextMenu();
        this.contextMenu = $('#contextMenu');
        this.mountContextMenu(e);
      },
      removePreviousContextMenu: function() {
        if (this.contextMenu) {
          this.contextMenu.hide();
          $(this.contextMenu).removeClass("dropup");
          this.contextMenu = null;
        }
      },
      mountContextMenu: function(e) {
        this.contextMenu[0].style.position = 'absolute';
        this.contextMenu[0].style.left = `${e.clientX}px`;
        this.contextMenu[0].style.top = `${e.clientY}px`;

        this.contextMenu.appendTo('body').show();

        let $ul = $(this.contextMenu).children(".dropdown-menu");
        let ulOffset = $ul.offset();

        // how much space would be left on the top if the dropdown opened that direction
        let spaceUp = (ulOffset.top - $ul.height()) - $(window).scrollTop();

        // how much space is left at the bottom
        let spaceDown = $(window).scrollTop() + $(window).height() - (ulOffset.top + $ul.height());

        // switch to dropup only if there is no space at the bottom AND there is space at the top,
        // or there isn't either but it would be still better fit
        if (spaceDown < 0 && (spaceUp >= 0 || spaceUp > spaceDown)) $(this.contextMenu).addClass("dropup");
      },
      loadViewer: function () {
        this.$emit('reload-manager');
        this.elements = null;
        this.getElementsFolder();
      }
    },
    beforeMount() {
      this.getElementsFolder();
    },
    mounted() {
      $('body > #contextMenu').remove();
      $('body > #contextMenuElement').remove();
      $('body').click(() => {
        this.removePreviousContextMenu()
      });
    },
    computed: {
      isLoaderEnabled: function () {
        return (this.$root.loader === false && !this.elements && !this.folderTree)
      },
    },
  }
</script>

<style scoped>

</style>