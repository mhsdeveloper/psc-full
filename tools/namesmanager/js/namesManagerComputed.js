
NamesManager.computed = {
}

NamesManager.filters = {
    truncate: function (value) {
      if (!value) return ''
      return (value.length < 250) ? value : value.substring(0, 250) + '...'
    }
  }

NamesManager.watch = {
    drawerContent: function () {
      this.selectedNames = []
    },
    showEditNameModal: function () {
      if (this.showEditNameModal === false) {
        this.resetNameObj()
      }
    },
    showNameModal: function () {
      if (this.showNameModal === false && this.showEditNameModal === false) {
        this.resetNameObj()
      }
    },
    showAddToGroupModal: function () {
      if (this.showAddToGroupModal === false) {
        this.selectedGroup = null
      }
    }
}

NamesManager.mounted = function() {
  this.$nextTick( () => {
    this.loadSettings();
  })
}