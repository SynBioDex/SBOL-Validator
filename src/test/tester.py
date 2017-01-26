import os
import validationapi.api as api
import unittest
import tempfile

class ValidationAPITestCase(unittest.TestCase):
    def test_post(self):
        rv = api.app.get('/validate')
        print(rv)


if __name__ == '__main__':
    unittest.main()
