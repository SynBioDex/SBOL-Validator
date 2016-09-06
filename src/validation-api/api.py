from flask import Flask
from flask_restful import Resource, Api


app = Flask(__name__)
api = Api(app)

class ValidationAPI(Resource):
    def post(self):
        pass

class UpdateAPI(Resource):
    def post(self):
        pass

api.add_resource(ValidationAPI, '/validate')

if __name__ == '__main__':
    app.run(debug=True)