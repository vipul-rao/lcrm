module.exports = {
    props: ['type', 'options'],
    data: function() {
        return {
            backup_type: ''
        }
    },
    mounted() {
        this.backup_type = this.type
    }
};
