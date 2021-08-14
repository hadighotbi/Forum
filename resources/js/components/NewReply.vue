<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
            <textarea name="body"
                      id="body"
                      class="form-control"
                      rows="5"
                      placeholder="Have somethig to say?"
                      v-model="body">
            </textarea>
            </div>
            <button type="submit"
                    class="btn btn-outline-primary"
                    @click="addReply">Post
            </button>
        </div>
        <p v-else class="text-center">Please <a href="/login"> Sign in </a>
            to participate in this discussion.
        </p>
    </div>
</template>

<script>
    import 'at.js';
    import 'jquery.caret';
    export default {

        data() {
            return {
                body: ''
            };
        },

        computed: {
            signedIn() {
                return Window.App.signedIn;
            }
        },

        mounted() {
            $('#body').atwho({
                at: "@",
                callbacks: {
                    remoteFilter: function(query, callback) {
                        $.getJSON("/api/users", {name: query}, function(usernames) {
                            callback(usernames)
                        });
                    }
                }
            });
        },

        methods: {
            addReply() {
                axios.post(location.pathname + '/replies', {body: this.body})

                    .catch(error => {
                        flash(error.response.data , 'danger');
                    })

                    .then(({data}) => {
                        this.body = '';
                        flash('Your reply has been posted.');
                        this.$emit('created', data);
                    });
            }
        }
    }
</script>
