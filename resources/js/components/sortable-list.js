export default function sortableList({ orderColumn }) {
    return {
        init() {
            const sortable = new Sortable(this.$el, {
                animation: 150,
                handle: '.sort-handle',
                onEnd: async ({ oldIndex, newIndex }) => {
                    if (oldIndex === newIndex) return
                    
                    // Update order in database
                    try {
                        await $wire.reorder(oldIndex, newIndex)
                    } catch (error) {
                        console.error('Failed to reorder:', error)
                    }
                },
            })
        }
    }
}