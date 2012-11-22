{
   "_id": "_design/author",
   "language": "javascript",
   "views": {
       "lastname": {
           "map": "function(doc) {\n       if (doc.author)\n       {\n          for (var i in doc.author)\n          {\n            if (doc.author[i].lastname)\n            {\n              emit(doc.author[i].lastname, doc._id);\n            }\n          }\n       }\n\n\n}"
       },
       "name": {
           "map": "function(doc) {\n       if (doc.author)\n       {\n          for (var i in doc.author)\n          {\n            if (doc.author[i].name)\n            {\n              emit(doc.author[i].name, doc._id);\n            }\n          }\n       }\n\n\n}"
       }
   }
}