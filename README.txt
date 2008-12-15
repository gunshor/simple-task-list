Some notes on this code test:

*) Many of these tasks I would never do. Instead, a good library/framework would
   be chosen to do them.  For testing purposes, I wanted to write all of the PHP
   from scratch.  Unfortunatley:
   
*) I apologize if this is a little overkill.  I have a hard time doing things 
   in a 'quick and dirty' way.  I started out  writing raw queries, but that 
   bugged me, so I refactored into the classes you see.  I hope that if it is
   overkill for what you were looking for, it at least demonstrates more of my
   capabilities.

   
Things I didn't do, but am aware are good practices:

*) Prepared statements.
*) Handling errors and exceptions gracefully



Improvements I would make if this was a large application:

*) Abstract the database schema from the handler class.