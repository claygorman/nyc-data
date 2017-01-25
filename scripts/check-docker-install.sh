command_exists() {
	string="$(command -v "$@" | grep --quiet "docker")"
	if [ $? = 1 ]; then
		return 1
	else
		return 0
	fi
}
semverParse() {
	major="${1%%.*}"
	minor="${1#$major.}"
	minor="${minor%%.*}"
	patch="${1#$major.$minor.}"
	patch="${patch%%[-.]*}"
}

if command_exists docker; then
	version="$(docker -v | cut -d ' ' -f3 | cut -d ',' -f1)"
	MAJOR_W=1
	MINOR_W=13

	semverParse $version

	shouldWarn=0
	if [ $major -lt $MAJOR_W ]; then
		shouldWarn=1
	fi

	if [ $major -le $MAJOR_W ] && [ $minor -lt $MINOR_W ]; then
		shouldWarn=1
	fi

	if [ $shouldWarn -eq 1 ]; then
		cat >&2 <<-'EOF'
		WARNING: it is suggested you upgrade docker to v1.13+.

		You can find instructions for this here:
		https://github.com/docker/docker/releases
		EOF
		( set -x; sleep 5 )
	else
		cat >&2 <<-'EOF'
		docker is installed and ok
		EOF
	fi
else
	echo "ERROR: you need to install docker to continue"
	exit 2
fi

if command_exists docker-compose; then
	version="$(docker-compose -v | cut -d ' ' -f3 | cut -d ',' -f1)"
	MAJOR_W=1
	MINOR_W=10

	semverParse $version

	shouldWarn=0
	if [ $major -lt $MAJOR_W ]; then
		shouldWarn=1
	fi

	if [ $major -le $MAJOR_W ] && [ $minor -lt $MINOR_W ]; then
		shouldWarn=1
	fi

	if [ $shouldWarn -eq 1 ]; then
		cat >&2 <<-'EOF'
		WARNING: it is suggested you upgrade docker-compose to v1.10+.

		You can find instructions for this here:
		https://github.com/docker/compose/releases
		EOF
		( set -x; sleep 5 )
	else
		cat >&2 <<-'EOF'
		docker-compose is installed and ok
		EOF
	fi
else
	echo "ERROR: you need to install docker-compose to continue";
	exit 2
fi