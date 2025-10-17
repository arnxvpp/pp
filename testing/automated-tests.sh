#!/bin/bash

###############################################################################
# PremierPlug WordPress Platform - Automated Testing Suite
# Version: 1.0.0
# Description: Comprehensive testing script for all platform components
###############################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counters
TESTS_RUN=0
TESTS_PASSED=0
TESTS_FAILED=0

# Site URL (update this)
SITE_URL="${SITE_URL:-http://localhost}"

echo "=========================================="
echo "PremierPlug Automated Testing Suite"
echo "=========================================="
echo "Site URL: $SITE_URL"
echo "Date: $(date)"
echo ""

# Function to run test
run_test() {
    local test_name=$1
    local test_command=$2

    TESTS_RUN=$((TESTS_RUN + 1))
    echo -n "Testing: $test_name ... "

    if eval "$test_command" > /dev/null 2>&1; then
        echo -e "${GREEN}PASS${NC}"
        TESTS_PASSED=$((TESTS_PASSED + 1))
        return 0
    else
        echo -e "${RED}FAIL${NC}"
        TESTS_FAILED=$((TESTS_FAILED + 1))
        return 1
    fi
}

echo "=========================================="
echo "1. CONNECTIVITY TESTS"
echo "=========================================="

run_test "Homepage loads (HTTP 200)" "curl -s -o /dev/null -w '%{http_code}' $SITE_URL | grep -q 200"
run_test "SSL certificate valid" "curl -s -k $SITE_URL | grep -q '<!DOCTYPE'"
run_test "Response time < 3 seconds" "[ $(curl -s -o /dev/null -w '%{time_total}' $SITE_URL | cut -d. -f1) -lt 3 ]"

echo ""
echo "=========================================="
echo "2. PAGE AVAILABILITY TESTS"
echo "=========================================="

# Test all main pages
pages=(
    "/"
    "/about-us"
    "/contact"
    "/careers"
    "/brand-consulting"
    "/brandmanagement"
    "/brand-studio"
    "/social-research"
    "/market-research"
    "/data-analysis"
    "/motion-pictures"
    "/digital-media"
    "/speakers"
    "/television"
    "/voiceovers"
    "/music-brand-partnerships"
    "/publishing"
)

for page in "${pages[@]}"; do
    run_test "Page: $page" "curl -s -o /dev/null -w '%{http_code}' $SITE_URL$page | grep -q 200"
done

echo ""
echo "=========================================="
echo "3. CONTENT VALIDATION TESTS"
echo "=========================================="

run_test "Homepage has title tag" "curl -s $SITE_URL | grep -q '<title>'"
run_test "Homepage has meta description" "curl -s $SITE_URL | grep -q 'meta name=\"description\"'"
run_test "Homepage has canonical URL" "curl -s $SITE_URL | grep -q 'rel=\"canonical\"'"
run_test "Navigation menu exists" "curl -s $SITE_URL | grep -q 'nav'"
run_test "Footer exists" "curl -s $SITE_URL | grep -q 'footer'"

echo ""
echo "=========================================="
echo "4. SEO TESTS"
echo "=========================================="

run_test "Sitemap.xml exists" "curl -s -o /dev/null -w '%{http_code}' $SITE_URL/sitemap.xml | grep -q 200"
run_test "Robots.txt exists" "curl -s -o /dev/null -w '%{http_code}' $SITE_URL/robots.txt | grep -q 200"
run_test "Sitemap in robots.txt" "curl -s $SITE_URL/robots.txt | grep -q 'Sitemap:'"
run_test "No duplicate title tags" "[ $(curl -s $SITE_URL | grep -c '<title>') -eq 1 ]"
run_test "Meta robots not blocking" "! curl -s $SITE_URL | grep -q 'noindex'"

echo ""
echo "=========================================="
echo "5. PERFORMANCE TESTS"
echo "=========================================="

run_test "GZIP compression enabled" "curl -s -H 'Accept-Encoding: gzip' -I $SITE_URL | grep -q 'Content-Encoding: gzip'"
run_test "Browser caching enabled" "curl -s -I $SITE_URL | grep -q 'Cache-Control'"
run_test "Images lazy loading" "curl -s $SITE_URL | grep -q 'loading=\"lazy\"'"
run_test "CSS minified" "curl -s $SITE_URL | grep -q '\.min\.css'"
run_test "JS deferred" "curl -s $SITE_URL | grep -q 'defer'"

echo ""
echo "=========================================="
echo "6. SECURITY TESTS"
echo "=========================================="

run_test "HTTPS redirect works" "[ $(curl -s -o /dev/null -w '%{http_code}' http://$(echo $SITE_URL | sed 's|https://||')) -eq 301 ]"
run_test "X-Frame-Options header" "curl -s -I $SITE_URL | grep -q 'X-Frame-Options'"
run_test "X-Content-Type-Options header" "curl -s -I $SITE_URL | grep -q 'X-Content-Type-Options'"
run_test "WordPress version hidden" "! curl -s $SITE_URL | grep -q 'wp-includes/version.php'"
run_test "No directory listing" "! curl -s $SITE_URL/wp-content/uploads/ | grep -q 'Index of'"

echo ""
echo "=========================================="
echo "7. FUNCTIONALITY TESTS"
echo "=========================================="

run_test "Search form exists" "curl -s $SITE_URL | grep -q 'search'"
run_test "Contact form exists" "curl -s $SITE_URL/contact | grep -q 'form'"
run_test "No 404 errors in assets" "! curl -s $SITE_URL | grep -q '404'"
run_test "No console errors visible" "! curl -s $SITE_URL | grep -q 'console.error'"

echo ""
echo "=========================================="
echo "8. MOBILE TESTS"
echo "=========================================="

run_test "Viewport meta tag" "curl -s $SITE_URL | grep -q 'viewport'"
run_test "Responsive images" "curl -s $SITE_URL | grep -q 'srcset'"
run_test "Touch-friendly buttons" "curl -s $SITE_URL | grep -q 'button'"

echo ""
echo "=========================================="
echo "9. WORDPRESS TESTS (if WP-CLI available)"
echo "=========================================="

if command -v wp &> /dev/null; then
    run_test "WordPress core up to date" "wp core check-update 2>&1 | grep -q 'Success'"
    run_test "No plugin updates needed" "[ $(wp plugin list --update=available --format=count) -eq 0 ]"
    run_test "Database optimized" "wp db optimize > /dev/null 2>&1"
    run_test "Permalinks configured" "wp option get permalink_structure | grep -q '/%postname%/'"
else
    echo -e "${YELLOW}WP-CLI not found, skipping WordPress tests${NC}"
fi

echo ""
echo "=========================================="
echo "10. ACCESSIBILITY TESTS"
echo "=========================================="

run_test "Alt tags on images" "curl -s $SITE_URL | grep '<img' | grep -q 'alt='"
run_test "Form labels exist" "curl -s $SITE_URL/contact | grep '<label' | grep -q 'for='"
run_test "Semantic HTML5 tags" "curl -s $SITE_URL | grep -q '<article\\|<section\\|<nav\\|<header\\|<footer'"
run_test "Skip to content link" "curl -s $SITE_URL | grep -q 'skip-to-content\\|skip-link'"

echo ""
echo "=========================================="
echo "TEST SUMMARY"
echo "=========================================="
echo "Total Tests Run: $TESTS_RUN"
echo -e "Tests Passed: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Tests Failed: ${RED}$TESTS_FAILED${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}=========================================="
    echo "ALL TESTS PASSED! ✓"
    echo -e "==========================================${NC}"
    exit 0
else
    echo -e "${RED}=========================================="
    echo "SOME TESTS FAILED!"
    echo -e "==========================================${NC}"
    echo "Please review failed tests and fix issues."
    exit 1
fi
