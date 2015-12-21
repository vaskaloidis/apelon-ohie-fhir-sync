#!/bin/sh

DAY=$(date +%d)
MONTH=$(date +%m)
YEAR=$(date +%y)
DATE=$(date +%Y-%m-%d)
YESTERDAY=$(date "--date=${DATE} -1 day" +%Y-%m-%d)

TAIL_COMMAND=""

if [ -f "/var/log/tomcat7/catalina.20$YEAR-$MONTH-$DAY.log" ]; then
	TAIL_COMMAND="$TAIL_COMMAND /var/log/tomcat7/catalina.20$YEAR-$MONTH-$DAY.log"
elif [ -f "/var/log/tomcat7/catalina.$YESTERDAY.log" ]; then
	TAIL_COMMAND="/var/log/tomcat7/catalina.$YESTERDAY.log"
fi

if [ -f "/var/log/tomcat7/catalina.20$YEAR-$MONTH-$DAY.log" ]; then
        TAIL_COMMAND="$TAIL_COMMAND /var/log/tomcat7/localhost.20$YEAR-$MONTH-$DAY.log"
elif [ -f "/var/log/tomcat7/catalina.$YESTERDAY.log" ]; then
        TAIL_COMMAND="/var/log/tomcat7/localhost.$YESTERDAY.log"
fi

tail -n 100 \
     -f /var/log/tomcat7/catalina.out \
        $(TAIL_COMMAND)
