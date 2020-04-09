<template>

	<li class="nav-item dropdown">
		<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">Notifications</a>

		<ul class="dropdown-menu" v-if="notifications.length" >
			<li v-for="notification in notifications" >
				<a :href="notification.data.link" v-text="notification.data.message" @click="markAsRead(notification)"></a>
			</li>
		</ul>
		<ul class="dropdown-menu" v-else>
			<li>
				<p>No Notifications</p>
			</li>
		</ul>
	</li>
	
</template>

<script>
	export default {

		data() {
			return { notifications: false }
		},

		created() {
			axios.get("/profiles/" + window.App.user.name + "/notifications")
			.then(response => this.notifications = response.data);
		},

		methods: {

			markAsRead(notification) {
				axios.delete("/profiles/" + window.App.user.name + "/notifications/" + notification.id);
			}
		}
	};
</script>
