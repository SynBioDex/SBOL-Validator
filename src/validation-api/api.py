from flask import Flask, request
from validator import ValidationOptions, ValidationResult, ValidationRun

app = Flask(__name__)

@app.route("/validate", methods=["POST"])
def validate(self):
    if not request.json
    options = new ValidationOptions();

    
@app.route("/update", methods=["POST"])
class UpdateAPI(Resource):
    def post(self):
        pass


if __name__ == '__main__':
    app.run(debug=True)