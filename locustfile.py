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
        self.client.get("/Frontend/VOLLEYBALL/scoreboard.php?match_id=d6fe3be89437af64bffc4e7685b6493a5625a64bdddeec3026289caa7bcf0629")

    @task
    def post_score(self):
        self.client.post("/Frontend/VOLLEYBALL/score_panel.php", {
            "match_id": "dc1dfd15feba6abd9db5a6c8d9101f7d1dacf1d249095aa14559b0b6a2529a5e",
            "runs": 4
        })

    @task
    def login(self):
        self.client.post("/front-page.php", {
            "email": "sonawanenileshk6@gmail.com",
            "password": "Nilesh@11"
        })
