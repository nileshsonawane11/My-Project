from locust import HttpUser, task, between

class WebsiteUser(HttpUser):
    wait_time = between(1, 5)  # Wait time between tasks

    # 👇 CHANGE THIS TO YOUR LOCAL OR LIVE URL
    host = "http://localhost/Project_files"
    # Example for hosted site:
    # host = "https://livestrike.in"

    @task
    def load_homepage(self):
        self.client.get("/")  # Home page

    @task
    def check_scores(self):
        self.client.get("/Frontend/CRICKET/scoreboard.php?match_id=dc3e950a014b157c98e5d702f09d838247d2c5615b6fba465589e640c7a61969")

    @task
    def post_score(self):
        self.client.post("/Frontend/CRICKET/score_panel.php", {
            "match_id": "dc3e950a014b157c98e5d702f09d838247d2c5615b6fba465589e640c7a61969",
            "runs": 4
        })

    @task
    def login(self):
        self.client.post("/front-page.php", {
            "email": "sonawanenileshk6@gmail.com",
            "password": "Nilesh@11"
        })
